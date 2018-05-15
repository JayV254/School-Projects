//
//  main.cpp
//  CS216-PA1
//  Purpose:  To hold a database read from a file, of actors and the movies in which they appear in. A menu will be repeatedly displayed to allow the user to search through the database and process various tasks related to elements in the database.
//  Input:  The user is allowed to input actors and movies in order to accomplish a specific task outlined in the option menu
//  Output:  The menu is repeatedly displayed until the user quits the program.  Certain elements in order are displayed according to what option the user chose
//  To Compile:  g++ -std=c++11 main.cpp
//  Created by Jesse Vaught on 2/23/17.
//  Copyright © 2017 Jesse Vaught. All rights reserved.
//

#include <iostream>
#include <sstream>
#include <string>
#include <fstream>
#include <map>
#include <set>
#include <vector>
#include <algorithm>

using namespace std;

int main(int argc, char* argv[])
{
    // Check whether the number of command line arguments is exactly one
    if (argc != 2)
    {
        cout << "Warning: need exactly one command line argument." << endl;
        cout << "Usage: " << argv[0] << " <inputfile_name>" << endl;
        return 1;
    }
    
    ifstream in_file;
    in_file.open(argv[1]);
    // Check whether the input file can be open successfully or not
    if (!in_file.good())
    {
        cout << "Warning: cannot open file named " << argv[1] << "!" << endl;
        return 2;
    }
    // Read data from the input file, and store into a map object: actors_db
    // the key is the actor name, the value is the set of movies which the actor(key) is in
    
    // Ask the user to type an actor's name (ignore case distinction),
    // and displays all the movies he/she is in
    // actor name is in upper case letters; movie names are in lower case.
    bool main_continue = true;
    set<string> all_movies;
    map<string, set<string>> actors_db;
    
    while (!in_file.eof())
    {
        string line;
        getline(in_file, line);
        string name, movie;
        istringstream iss(line);
        getline(iss, name,',');
        
        // Extract extra white space
        iss>>ws;
        
        // Create a new set of movies assocatiated with name
        set<string> movies, actors;
        
        while (getline(iss, movie, ','))
        {
            movies.insert(movie);
            all_movies.insert(movie);
            // extract white space
            iss>>ws;
        }
        // If the key does not exist, add the key to the map actors_db
        // If the key exists, then do nothing
        if (actors_db.find(name) == actors_db.end())
            actors_db.insert(pair<string, set<string> >(name, movies));
    }
    // close the input file
    in_file.close();
    while(main_continue)
    {
        // Display main menu
        cout << "This application stores information about Actors, and their Movies." << endl;
        cout << "Please choose an option from the menu below (Enter Q or q to quit): " << endl;
        cout << "1. Actors in movies" << endl;
        cout << "2. Actors and co-actors" << endl;
        cout << "Enter Option: ";
        // Pull in input from user input in menu1
        int option;
        cin >> option;
        // select switch case based upon main menu input
        switch(option)
        {
            // If user enters 1
            case 1:
            {
                //Ask for two movie input, and check to see if they are in the database
                string movie1,movie2;
                cout << "Please input the first movie title: ";
                cin.ignore();
                getline(cin, movie1);
                cout << "Please input the second movie title: ";
                getline(cin, movie2);
                //initialize sentinel to false "quit"
                bool quit = false;
                //If both movies are in the database
                if (all_movies.count(movie1) > 0 && all_movies.count(movie2) > 0)
                {
                    cout << "\nBoth movies are in the database, please continue..." << endl;
                    while(quit != true)
                    {
                        //Display option menu associated with main menu choice 1
                        cout << "Please input your menu option (enter Q or q to quit)" << endl;
                        cout << "A -- to print all the actors in either of the two movies." << endl;
                        cout << "C -- to print all the common actors in both of the movies." << endl;
                        cout << "O -- to print all the actors who are in one movie, but not in both." << endl;
                        char option_menu1;
                        cout << "Option: ";
                        cin >> option_menu1;
                        switch(option_menu1)
                        {
                            //If user enters A or a
                            case 'a':
                            case 'A':
                            {
                                map<string, set<string>>::iterator i;
                                cout << "All of the actors in either of the two movies" << endl;
                                for (i=actors_db.begin(); i != actors_db.end(); i++)
                                {
                                    if (i->second.count(movie1) > 0 || i->second.count(movie2) > 0)
                                    {
                                        cout << i->first << endl;
                                    }
                                }
                                cout << endl;
                                break;
                            
                            }
                            //If user enters C or c
                            case 'c':
                            case 'C':
                            {
                                map<string, set<string>>::iterator i;
                                cout << "All of the actors who appear in both movies"<<endl;
                                for (i=actors_db.begin(); i != actors_db.end(); i++)
                                {
                                    if (i->second.count(movie1) > 0 && i->second.count(movie2) > 0)
                                    {
                                        cout << i->first << endl;
                                    }
                                }
                                cout << endl;
                                break;
                            }
                            //If user enters O or o
                            case 'o':
                            case 'O':
                            {
                                map<string, set<string>>::iterator i;
                                cout << "All of the actors who appear in only one of the movies"<<endl;
                                for (i=actors_db.begin(); i != actors_db.end(); i++)
                                {
                                    if (!(i->second.count(movie1) > 0) != !(i->second.count(movie2) > 0))
                                    {
                                        cout << i->first << endl;
                                    }
                                }
                                cout << endl;
                                break;
                            }
                            //If user enters Q or q
                            case 'Q':
                            case 'q':
                            {
                                //Set sentinel value to true, which will break option menu1 loop
                                quit = true;
                                break;
                            }
                            //Any other values will default to invalid choice and re-run option menu1
                            default:
                            {
                                cout << "Invalid choice!";
                                break;
                            
                            }
                            
                        }
                    }
                }
                //If at least one movie is not in the database tell the user and re-run option menu1
                else
                {
                    cout << "At least one movie you entered is not in the database!"<< endl;
                    cout << endl;
                    continue;
                }
                break;
            }
            // If user enters 2 for option menu2
            case 2:
            {
                cout << "Finding the co-actors of the actor by typing his/her name: ";
                string actor_name;
                cin.ignore();
                getline(cin, actor_name);
                //If the actors name exist in the database, continue the search process
                if (actors_db.find(actor_name)!= actors_db.end())
                {
                    set<string> compare_set;
                    map<string, vector<string>> co_actors;
                    map<string, vector<string>>:: iterator co_it;
                    map<string, set<string>>:: iterator it;
                    
                    it = actors_db.find(actor_name);
                    compare_set = it->second;
                    map<string, set<string>>::iterator i;
                    actors_db.find(actor_name);
                    
                    //Loop through the actor_db and find actors that appear in a movie matching the compare set(movies with user inputted actor name)
                    for (i=actors_db.begin(); i != actors_db.end(); i++)
                    {
                        string name_for_map = i->first;
                        vector<string> compare;
                        set_intersection(compare_set.begin(), compare_set.end(), i->second.begin(), i->second.end(),back_inserter(compare));
                        if(compare.size() > 0)
                        {
                            //Loop through the common movies associated with iterator->first (name) and make new map containing common movies as the key and actors associated with those common movies as the value
                            for(int i = 0;i < compare.size();i++)
                            {
                                //If key exists
                                if (co_actors.find(compare[i]) != co_actors.end())
                                {
                                    co_it = co_actors.find(compare[i]);
                                    co_it->second.push_back(name_for_map);
                                }
                                else
                                {
                                    vector<string> names;
                                    if(name_for_map != actor_name)
                                    {
                                        names.push_back(name_for_map);
                                        co_actors.insert(pair<string,vector<string> > (compare[i],names));
                                    }
                                }
                            }
                        }
                    }
                    for(co_it=co_actors.begin();co_it != co_actors.end();co_it++)
                    {
                        cout << "The co-actors of " << actor_name << " in the movie " << "\"" << co_it->first << "\"" <<" are: " << endl;
                        for(int i = 0; i < co_it->second.size();i++)
                        {
                            if(co_it->second[i] != actor_name)
                            {
                                cout <<"-" << co_it->second[i]<< "-" << endl;
                            }
                        }
                        cout << "*************************" << endl;
                    }
                    cout << endl;
                }
                break;
            }
            // Check default for any values that aren't 1 or 2.  If user entered Q or Q, break the loop and exit program, else display invalid input and break to main menu
            default:
            {
                if(cin.fail())
                {
                    string choice;
                    cin.clear();
                    cin >> choice;
                    
                    if (choice == "Q" || choice == "q")
                    {
                        main_continue = false;
                        break;
                    }
                    else
                    {
                        cout << "Invalid input, please try again!" << "\n" << endl;
                    }
                }
                else
                    cout << "Invalid input, please try again!" << "\n" << endl;
            }
        }
    }
}
