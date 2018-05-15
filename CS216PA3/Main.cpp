//course: CS216-004
//Project: Program 3
//Date: 04/18/2017
//Purpose: create a graph from given set of vertices and edges
//         repeatedly ask the user to choose a source vertex (enter "Q" or "q" to quit)
//         and calculate the shortest distance of each vertex to the source
//         then display the path from every vertex to the source
//         It is a sub-problem of Project 3.
//Author: (Jesse Vaught)

#include <iostream>
#include <vector>
#include "Graph.h"
#include <sstream>
#include <fstream>
#include <map>
#include <set>

using namespace std;

string toLower(string input)
{
    for(int i = 0; i < input.length(); i++)
        input[i] = tolower(input[i]);
    return input;
}

bool compare(string input1, string input2)
{
    if(toLower(input1) == toLower(input2))
        return true;
    else
        return false;
}

int main()
{
    // Initalize input file streams for each of the three files
    ifstream actorList, movieList, movieactorList;
    string actor_list, movie_list, movie_actor_list;
    
    // Initalize the maps for actor and movie input file readins
    map <int, pair<string,int>> actor_map;
    map <int, string> movie_map;
    map <int, vector<int>> movie_actor_map;
    map <int, string> vertex_actor_map;
    
    // Initalize actor map variables
    int actor_ID;
    int vertex = -1;
    string actor_name;
    
    // Prompt for actor list file
    cout << "Please enter the file name of the desired actor list: ";
    cin >> actor_list;
    actorList.open(actor_list);
    if(!actorList.is_open())
    {
        // Open default file "actors.txt"
        actorList.open("actors.txt");
        
        // If default can't be opened, display error message
        if(!actorList.is_open())
        {
            cout << "Can't open specified file or default file" << endl;
            return 0;
        }
    }
    // Read in good file and store elements into map
    while (!actorList.eof())
    {
        actorList >> actor_ID;
        vertex++;
        actorList >> ws;  // extract and ignore the blank space
        if(actorList.peek() == '|')
            actorList.ignore();
        getline(actorList, actor_name);
        pair<string,int> actor_element (actor_name, vertex);
        actor_map.insert(pair<int,pair<string,int>> (actor_ID, actor_element));
    }
    
    // close the file after finishing reading data from actor txt file
    actorList.close();
    
    // Initalize movie map variables
    int movie_ID;
    string movie_name;
    
    // Prompt for movie list file
    cout << "Please enter the file name of the desired movie list: ";
    cin >> movie_list;
    movieList.open(movie_list);
    if(!movieList.is_open())
    {
        // Open default file "movies.txt"
        movieList.open("movies.txt");
        
        // If default can't be opened, display error message
        if(!movieList.is_open())
        {
            cout << "Can't open specified file or default file" << endl;
            return 0;
        }
    }
    
    // Read in good file and store elements into map
    while (movieList >> movie_ID)
    {
        movieList >> ws;  // extract and ignore the blank space
        if(movieList.peek() == '|')
            movieList.ignore();
        getline(movieList, movie_name);
        pair<int,string> movie_element (movie_ID, movie_name);
        movie_map.insert(movie_element);
    }
    
    //  Close movie file after finishing reading from movie txt file
    movieList.close();
    
    // Initalize movie map variables
    int movie_ID_edge, actor_ID_vertex;
    vector<int> actor_ID_vertices;
    
    // Initalize movie actor graph
    Graph actor_graph(actor_map.size());
    
    // Prompt for movie list file
    cout << "Please enter the file name of the desired movie-actor list: ";
    cin >> movie_actor_list;
    movieactorList.open(movie_actor_list);
    if(!movieactorList.is_open())
    {
        // Open default file "movies.txt"
        movieactorList.open("movie-actor.txt");
        
        // If default can't be opened, display error message
        if(!movieactorList.is_open())
        {
            cout << "Can't open specified file or default file" << endl;
            return 0;
        }
    }
    
    // Read in good file and store elements into map
    while (movieactorList >> movie_ID_edge)
    {
        movieactorList >> ws;  // extract and ignore the blank space
        if(movieactorList.peek() == '|')
            movieactorList.ignore();
        movieactorList >> actor_ID_vertex;
        auto actor_it = actor_map.find(actor_ID_vertex);
        int actual_vertex = actor_it->second.second;
        if (movie_actor_map.find(movie_ID_edge) == movie_actor_map.end())
        {
            actor_ID_vertices.clear();
            actor_ID_vertices.push_back(actual_vertex);
            pair<int,vector<int>> movie_actor_element (movie_ID_edge, actor_ID_vertices);
            movie_actor_map.insert(pair<int, vector<int>>(movie_actor_element));
        }
        else
        {
            auto movieactor_it = movie_actor_map.find(movie_ID_edge);
            for(int i=0; i < movieactor_it->second.size(); i++)
            {
                actor_graph.addEdge(movieactor_it->second[i], actual_vertex, movie_ID_edge);
            }
            movieactor_it->second.push_back(actual_vertex);
        }
    }
    
    //  Close movie file after finishing reading from movie txt file
    movieactorList.close();
    
    // Map vertex to actor name
    for(auto vertex_it = actor_map.begin(); vertex_it != actor_map.end(); vertex_it++)
    {
        pair<int,string> vector_actor_element (vertex_it->second.second, vertex_it->second.first);
        vertex_actor_map.insert(vector_actor_element);
    }
    while (true)
    {
        cout <<"*******************************************************************" << endl << "The Bacon number of an actor is the number of degrees of separation he/she has from Bacon. Those actors who have worked directly with Kevin Bacon in a movie have a Bacon number of 1.";
        cout << endl << "This application helps you find the Bacon number of an actor." << endl << "Enter ""exit"" to quit the program." << endl;
        cout << "Please enter an actor's name (case-insensitive): " << endl;
        string actor_name_input, actor_name_input1,actor_name_input2;
        cin >> actor_name_input1;
        getline(cin, actor_name_input2);
        actor_name_input = actor_name_input1 + actor_name_input2;
        
        
        
        // If input fails check it and respond accordingly
        if (cin.fail())
        {
            string check_input;
            cin.clear();
            cin >> check_input;
            cout << "Invalid input, please try again..." << endl;
            continue;
        }
        
        // If input is exit key, break loop
        else if(compare(actor_name_input, "exit"))
        {
            return 0;
        }
        
        // If input is good
        else
        {
            int destination = -1, source = -1;
            for(auto find_vertex = actor_map.begin(); find_vertex != actor_map.end(); find_vertex++)
            {
                if(compare(find_vertex->second.first, actor_name_input))
                {
                    destination = find_vertex->second.second;
                }
                
                if(compare(find_vertex->second.first, "Kevin Bacon"))
                {
                    source = find_vertex->second.second;
                }
            }
            if(source == -1 || vertex == -1)
            {
                if(source == -1)
                    cout << "Kevin Bacon does not appear in the actor input file!";
                else
                    cout << "The actor you chose does not appear in the actor input file!";
            }
            
            // Good input perform BFS
            else
            {
                // Initalize vector that will hold path distance, and vector for holding parent vertex
                vector<int> distance(actor_map.size(), -1);
                vector<int> go_through(actor_map.size(), -1);
                actor_graph.BFS(source, distance, go_through);
                int bacon_number= distance[destination];
                
                // If there is a path to the destination from source
                if(bacon_number != -1)
                {
                    cout << "The Bacon number for " << actor_name_input << " is: " << distance[destination] << endl;
                    cout << endl;
                    
                    vector<int> path;
                    int current = destination;
                    path.push_back(destination);
                    for(int i= 0; i < bacon_number; i++)
                    {
                        path.push_back(go_through[current]);
                        current = go_through[current];
                    }
                    for(int i = 0; i < path.size() - 1; i++)
                    {
                        // Find common movie first
                        int edge = actor_graph.getEdge(path[i], path[i + 1]);
                        auto it = movie_map.find(edge);
                        string movie_name = it->second;
                        
                        // Find actors
                        auto current_actor = vertex_actor_map.find(path[i]);
                        auto next_actor = vertex_actor_map.find(path[i+1]);
                        cout << current_actor->second <<  " appeared in " << movie_name << " with " <<  next_actor->second << endl;
                    }
                }
                
                // If there is not a path to the destination from the source
                else
                    cout << "There is no path between Kevin Bacon and " << actor_name_input << endl;
            }
        }
    }
    return 0;
}

