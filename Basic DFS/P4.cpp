// Practicum 4 solution
// Jesse Vaught
// CS315
// 4/6/17

#include <iostream> 
#include <sstream> 
#include <string> 
#include <cstring> // memset, strncpy
#include <fstream>

using namespace std;

// WALL = 5; to protect agains going out of bounds -- change for other applications!
char matrix[100 + 5][100 + 5];  //matrix[100 + WALL][100 + WALL];
bool visited[100 + 5][100 + 5];
// size of map (matrix)
int row;
int col;

void findFly(int r, int c, int &num_flies)
{
    int r_bound = row - 1;
    int c_bound = col -1;
    //  If cell is sugar or has been visited, return and continue exploring
    if(matrix[r][c] == 'S' || visited[r][c])
    {
        if(matrix[r][c] == 'S')
            visited[r][c] = true;
        return;
    }
    //  If cell is Fly return true, increment flies and continue exploring
    if(matrix[r][c] == 'F')
    {
        visited[r][c] = true;
        num_flies += 1;
        return;
    }
    visited[r][c] = true;
    
    //  If current exploration is visited, or a special boundary case check the cell.
    if (r > r_bound || r < 0 || c > c_bound || c < 0)
    {
        //  Take care of all boundary cases where wraparound is necessary in light of "torus" shape
        if(c > c_bound)
            if(r == r_bound)
            {
                //bottom right corner exploring to the right
                findFly(r_bound,0,num_flies);
            }
            else if(r == 0)
            {
                //top right corner exploring to the right
                findFly(0,0,num_flies);
            }
            else
            {
                //boundary case on the right side of the matrix traveling right
                findFly(r, 0,num_flies);
            }
        else if(c < 0)
        {
            if(r == r_bound)
            {
                // bottom left exploring to the left
                findFly(r_bound,c_bound,num_flies);
            }
            else if(r == 0)
            {
                //  top left exploring to the left
                findFly(0,c_bound,num_flies);
            }
            else
            {
                //  boundary case on the left side of the matrix traveling left
                findFly(r, c_bound,num_flies);
            }
        }
        else if(r > r_bound)
        {
            if(c == c_bound)
            {
                //  bottom right corner traveling down in the matrix
                findFly(0, c_bound,num_flies);
            }
            else if(c == 0)
            {
                //  bottom left corner traveling down
                findFly(0,0,num_flies);
            }
            else
            {
                //  boundary case on the bottom of the matrix traveling down
                findFly(0, c,num_flies);
            }
        }
        else if(r < 0)
        {
            if(c == c_bound)
            {
                //  top right corner traveling up
                findFly(r_bound,c_bound,num_flies);
            }
            else if(c == 0)
            {
                //  top left corner traveling up
                findFly(r_bound, 0,num_flies);
            }
            else
            {
                //  boundary case on the top of the matrix traveling up
                findFly(r_bound, c,num_flies);
            }
        }
    }
    else
    {
    //dfs called for 8 neighbors of the current cell
        findFly(r, c + 1, num_flies);
        findFly(r, c - 1, num_flies);
        findFly(r + 1 , c, num_flies);
        findFly(r + 1 , c + 1, num_flies);
        findFly(r + 1 , c - 1, num_flies);
        findFly(r - 1 , c, num_flies);
        findFly(r - 1 , c + 1, num_flies);
    }
        
    return;
}

int main(int argc, char* argv[])  //main: to handle input, dfs calls, and to output
{
    // Check whether the number of command line arguments is exactly one
    if (argc != 2)
    {
        cout << "Warning: need exactly one command line argument." << endl;
        cout << "Usage: " << argv[0] << " <inputfile_name>" << endl;
        return 1;
    }
    
    ifstream in_file;
    in_file.open("test.txt");
    // Check whether the input file can be open successfully or not
    if (!in_file.good())
    {
        cout << "Warning: cannot open file named " << argv[1] << "!" << endl;
        return 2;
    }

    string line;
    string answer;
    while (!in_file.eof())
    {
        row = col = 0;
        while (getline(in_file, line) && line.size() > 0 )
        {
            if (line[0] == 'D' || line[0] == 'F' || line[0] == 'S')
            {
                // this line is a part of the map (row)
                strncpy(matrix[row], line.c_str(), sizeof(matrix[row]));
                col = line.size();
                row++;
            }
            else
            {
                // a query (not a map/row)
                int r, c;
                istringstream line_in(line);
                line_in >> r >> c;
                int num_flies = 0;
                findFly(r-1,c-1, num_flies);
                if(num_flies > 0)
                    answer = "YES";
                else
                    answer = "NO";
                cout << answer << endl;
                cout << "Flies found: " << num_flies << endl;
                // (r,c) is stored in (r-1,c-1)
                // Initialize all cells as unvisited for the next query.
                memset(visited, 0, sizeof(visited));
            }
        }
    }
        return 0;
}
