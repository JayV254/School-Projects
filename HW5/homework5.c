#include <stdio.h>
#include <string.h>
#include <stdlib.h>
#include <unistd.h>
#include <errno.h>
#include <sys/types.h>
#include <sys/wait.h>
#include <sys/stat.h>
#include <fcntl.h>


void getCommandFromUser (char command[]);
int find_type(char command[]);
int split_parse(char command[], char *part1[], char *part2[]);
void  Execute_Regular_Command(char command[], char *part1[], char *part2[]);
void  Execute_Pipe_Command(char command[], char *part1[],char *part2[]);
void  Execute_Redirection_Command(char command[], char *part1[],char *part2[]);


int main() {
    int type = 0;
    char commandline[256];
    char *part1[128];
    char *part2[128];
    printf ("\nMyshell> "); 
    getCommandFromUser(commandline);
    
    while((strcmp (commandline,"exit") != 0)
        && (strcmp (commandline,"quit") != 0))
    {
        type = find_type(commandline);
        if (type == -1)
             Execute_Regular_Command(commandline, part1, part2);
        else if (type==2)
   	        Execute_Pipe_Command(commandline, part1, part2);
        else if (type==3)
   	        Execute_Redirection_Command(commandline, part1,part2);
        
        // clear command line
        memset(&commandline[0], 0, sizeof(commandline));
        int part1count = 0;
        int part2count = 0;
        
        //clear part1
        while(part1[part1count] != NULL)
        {
            part1[part1count] = NULL;
            part1count++;
        }
        //clear part2
        while(part2[part2count] != NULL)
        {
            part2[part2count] = NULL;
            part2count++;
        }
        printf ("\nMyshell> ");
        getCommandFromUser (commandline);

     }
}

int find_type(char command[]) {
    int type = -1;
    for(int i=0; i < strlen(command); i++){
        if(command[i] == '>' && command[i+1] == '>') {
            type = 3;
        } else if(command[i] == '|') {
            type = 2;
        }
    }
    return type;
}


int split_parse(char command[], char *part1[], char *part2[]) {
    
    int background = 1;
    
    // initialize counters for the part1 and part2 arrays
    int part1_count = 0;
    int part2_count = 0;
    int switch_flag = 0;
    char *p = strtok(command, " ");
    
    // while grabbed token is not NULL
    while (p != NULL)
    {
        // if the current value tokenized value is equal to the pipe or append symbol ">>" or "|"
        if(strcmp(p, ">>") == 0 || strcmp(p, "|") == 0){
            switch_flag = 1;
        } else if(strcmp(p,"&") == 0) {
            background = 0;
        } else {
        // else if the switch flag hasn't been set, add to part1 array
            if(switch_flag == 0) {
                part1[part1_count] = strdup(p);
                part1_count++;
            }
        // else switch has been toggle add token to part2 array
            else {
                part2[part2_count] = strdup(p);
                part2_count++;
            }
        }
        // pass NULL value to tokenizer to allow it to pickup where it left off
        p = strtok(NULL, " ");
    }
    // clear temp array
    return background;
}

void getCommandFromUser (char command[]){
    scanf(" %[^\n]s", command);
}

void Execute_Regular_Command(char command[], char *part1[], char *part2[]){
    int pid;
    int background = split_parse(command, part1, part2);
    if ((pid = fork()) == 0)
    {
        execvp(part1[0], part1);
        perror("execvp failed");
        exit(1); 
    }
    else					
    {  
        if(!background)				
            waitpid(pid, NULL, 0);			     
    }
}

void  Execute_Pipe_Command(char command[], char *part1[],char *part2[]) {
    int fds[2];
    pid_t id1,id2;
    pipe (fds);
    int background = split_parse(command, part1, part2);
    
    if ((id1 =fork())< 0){
        perror("fork failed:");
        exit(1);
    }
    if (id1==0){
        dup2(fds[0],0);
        close(fds[1]);
        execvp(part2[0],part2);
        perror("execvp failed");
    }
    if ((id2 =fork())< 0){
        perror("fork failed:");
        exit(2);
    }
    
    if (id2 == 0) {
        dup2(fds[1],1);
        close(fds[0]);
        execvp(part1[0],part1);
        perror("execvp failed");
    }
    else{
        close(fds[0]);
        close(fds[1]);
        if(!background){
            waitpid(id1,NULL,0);
            waitpid(id2,NULL,0);
        }
    }
}

void  Execute_Redirection_Command(char command[], char *part1[],char *part2[]) {
    int fds[2];
    int count;
    int fd;
    pid_t pid1,pid2;
    char c;
    pipe (fds); // create a pipe and handover to children
    int background = split_parse(command, part1, part2);
    if ((pid1=fork()) < 0) {
        perror("fork failed:");
        exit(1);
    }
    if (pid1==0) {//code for child 1 starts
        dup2(fds[1],1);// Child 1 connects stdout to upstream end of pipe and
        close(fds[0]);// closes the downstream end of the pipe
        execvp(part1[0],part1);
    }//code for child 1 ends
    
    if ((pid2=fork())<0){
        perror("fork failed:");
        exit(2);
    }
    
    if (pid2==0){ //code for child 2 starts
        fd=open(part2[0], O_RDWR|O_CREAT,0600);
        dup2(fds[0],0); // Child 2 connects stdin to downstream end of pipe and
        close(fds[1]);// closes the upstream end of the pipe
        while ((count=read(0,&c,1))>0)
            write(fd,&c,1);
        close(fd);
        exit(1);
    } //code for child 2 ends
    else {//parent code starts
        close(fds[0]); //parent closes one end of the pipe
        close(fds[1]); //parent closes the other end of the pipe
        if(!background) {
            waitpid(pid1,NULL,0);
            waitpid(pid2,NULL,0);
        }
    }//parent code ends
}
