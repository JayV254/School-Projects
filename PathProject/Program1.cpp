//
//  main.cpp
//  CS315-PA1
//  Purpose:  To hold a database of cities and their respective coordinates, and then to output specific paths connecting the cities in the respective direction(W to E, N to S)
//  Input:  The only input for this program is a txt file in specified format listing cities and their decimal degree cooridinates
//  Output: The list of cities in order.. depending on their decimal cooridinates in a W-E or N-S path
//  To Compile:  g++ -std=c++11 main.cpp
//  Created by Jesse Vaught on 2/23/17.
//  Copyright © 2017 Jesse Vaught. All rights reserved.

#include <iostream>
#include <sstream>
#include <string>
#include <fstream>
#include <map>
#include <set>
#include <vector>
#include <algorithm>
#include <cmath>
#include <iomanip>

const double PI  = 3.141592653589793238463;
const double K2M = 0.621371;

using namespace std;


// Rounds double to 4 decimal places
double round_4_decimals(double x)
{
    double rounded;
    if(x > 0)
    {
        rounded = floor(x * 10000 + 0.5)/10000;
    }
    else
        rounded = ceil(x * 10000 - 0.5)/10000;
    return rounded;
}

// Converts degrees to radians
double deg2rad(double deg)
{
    return (deg * PI / 180);
}

// Converts radians to degrees
double rad2deg(double rad)
{
    return (rad * 180 / PI);
}

// Haversine Formula: Compute distances between two coordinate points in kilometers
double haversine(double lat1, double lon1, double lat2, double lon2)
{
    // convert all coordinates from degrees to radians using deg2rad function
    lat1 = deg2rad(lat1);
    lat2 = deg2rad(lat2);
    lon1 = deg2rad(lon1);
    lon2 = deg2rad(lon2);
    
    // calculate haversine
    double lng = lon2 - lon1;
    double lat = lat1 - lat2;
    double d = pow(sin(lat * 0.5), 2) + (cos(lat1) * cos(lat2) * pow(sin(lng * 0.5), 2));
    double h = 2 * 6371 * asin(sqrt(d));
    return h;
}

// Takes file line as input, splits the line into a city and its cooridinates, then inserts the city/coord pair into a map for further implementation.
void map_inserter(string whole_string, map<string, pair<double,double>> &city_coords)
{
    string city_name;
    int end_index;
    int coords_start_index;
    int start_loc=0;
    pair<double,double> lat_long;
    
    // Loop through txt file line
    for(int i = 0;i < whole_string.size(); i++)
    {
        // If a digit or minus sign is found, index the position and substring the former part of the line
        if(isdigit(whole_string[i]) || whole_string[i] == '-')
        {
            coords_start_index = i;
            int tmp_end_index = i;
            while(whole_string[tmp_end_index-1] == ' ')
                tmp_end_index -= 1;
            end_index = tmp_end_index;
            city_name = whole_string.substr(start_loc,(end_index));
            break;
        }
    }
    
    // Use the rest of the string (after city name) and split the two double values into a pair.
    string coords = whole_string.substr(coords_start_index);
    stringstream(coords) >> lat_long.first >> lat_long.second;
    lat_long.first = round_4_decimals(lat_long.first);
    lat_long.second = round_4_decimals(lat_long.second);
    
    // If the city key exists in the map, do nothing.  If it does not exist, add it.
    if (city_coords.find(city_name) == city_coords.end())
        city_coords.insert(pair<string, pair<double,double>>(city_name, lat_long));
}

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
    
    // Initialize the map object used to store the city and its respective coords
    map<string, pair<double,double>> city_coords;
    string line;
    
    // While line can be read from file
    while (getline(in_file, line))
    {
        // Use "map_inserter" function to piece apart the txt file line and create a map element.  Insert map element into city_coords map object
        map_inserter(line, city_coords);
    }
    // close the input file
    in_file.close();
    
    /* STEP 2: CONVERT MAP INTO LIST OF VALUES/KEYS */
    unsigned long num_cities = city_coords.size();
    vector<pair<double,double>> list_ordering_WE, list_ordering_NS;
    for( const auto & entry : city_coords)
    {
        list_ordering_NS.push_back(entry.second);
        list_ordering_WE.push_back(entry.second);
        // list_ordering.push_back( entry.first ); <- Use this line if you want a list of keys
    }
    
    /* STEP 3: FIND ORDERINGS */
    // West to East ordering- All cities are limited to a small coordinate grid: can exclude further sorting based upon 180 degree scale
    sort(list_ordering_WE.begin(), list_ordering_WE.end(), [](const pair<double,double> &left, const pair<double,double> &right)
    {
        return left.second < right.second;
    });
    
    // North to South ordering- All cities are limited to a small coordinate grid: can exclude further sorting based upon the 180 degree scale
    sort(list_ordering_NS.begin(), list_ordering_NS.end(), [](const pair<double,double> &left, const pair<double,double> &right)
         {
             return left.first > right.first;
         });
    
    // Find the first and last city of each sorted list (North and South/East and West)
    string North_city, South_city, West_city, East_city;
    map<string, pair<double,double>>::iterator it;
    for (it=city_coords.begin(); it != city_coords.end(); it++)
    {
        if((it->second.first == list_ordering_NS[0].first) && (it->second.second == list_ordering_NS[0].second))
        {
            North_city = it->first;
        }
        if((it->second.first == list_ordering_NS[num_cities -1].first) && (it->second.second == list_ordering_NS[num_cities-1].second))
        {
            South_city = it->first;
        }
        if((it->second.first == list_ordering_WE[0].first) && (it->second.second == list_ordering_WE[0].second))
        {
            West_city = it->first;
        }
        if((it->second.first == list_ordering_WE[num_cities -1].first) && (it->second.second == list_ordering_WE[num_cities-1].second))
        {
            East_city = it->first;
        }

        
    }
    // Find first city and last city in path
    
    /* STEP 4: COMPUTE DISTANCES */
    // For: WEST to EAST
    double path_distance_WE = 0;
    for(int i = 0; i < num_cities - 1; i++)
    {
        // Implement Haversine Formula to add distances between cities in list
        path_distance_WE += haversine(list_ordering_WE[i].first, list_ordering_WE[i].second, list_ordering_WE[i+1].first, list_ordering_WE[i+1].second);
    }
    
    //  For: NORTH to SOUTH
    double path_distance_NS = 0;
    for(int i = 0; i < num_cities - 1; i++)
    {
        // Implement Haversine Formula to add distances between cities in list
        path_distance_NS += haversine(list_ordering_NS[i].first, list_ordering_NS[i].second, list_ordering_NS[i+1].first, list_ordering_NS[i+1].second);
    }
    
    
    /* STEP 5: PRINT RESULT */
    
    cout << "WE: " << West_city << " - " << East_city << endl;
    cout << round(path_distance_WE) << " km" << endl;
    cout << round(path_distance_WE * K2M) << " miles" << endl;
    cout << "NS: " << North_city << " - " << South_city << endl;
    cout << round(path_distance_NS) << " km" << endl;
    cout << round(path_distance_NS * K2M) << " miles" << endl;
    
    return 0;
}


