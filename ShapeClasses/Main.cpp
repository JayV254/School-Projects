//
//  Main.cpp
//  VaughtProgram2
//  CS215-004
//  Created by Jesse Vaught on 11/13/16.
//  Copyright © 2016 Jesse Vaught. All rights reserved.
//  Purpose:  To read in a file, and display certain calculations depending on objects created through the file contents.  After contents are displayed, the object with the greatest volume and SA is displayed.
//  Input: file and number of arguments in command line
//  Output:  displays measurements and object, as well as the greatest Volume and greatest SA
//  Contains:  Header files and defined classes

#include"Shape.h"
#include"Circle.h"
#include"Rectangle.h"
#include"Cylinder.h"
#include"Cuboid.h"
#include"Sphere.h"
#include<iostream>
#include<fstream>
#include<vector>
#include<sstream>
#include<string>

using namespace std;
//  Purpose:  To read in a vector of pointers to shape objects, and display the object that has the greatest Surface Area
//  Input: Vector of shape object pointers
//  Output:  displays measurements and object, as well as the greatest SA
void maxSurfaceArea(vector<Shape*> shapes)
{
    int current_max = 0;
    Shape* maxSA = NULL;
    
    for(int i = 0; i < shapes.size(); i++)
        if(shapes[i]->computeArea() > current_max)
        {
            current_max = shapes[i]->computeArea();
            maxSA = shapes[i];
        }
    cout << "The shape with the largest surface area is:"<<endl;
    maxSA->display();
    cout<<endl;
}
//  Purpose:  To read in a vector of pointers to shape objects, and display the object that has the greatest Volume
//  Input: Vector of shape object pointers
//  Output:  displays object that has the greatest Volume as well as its contents
void maxVolume(vector<Shape*> shapes)
{
    int current_max = 0;
    Shape* maxV = NULL;
    
    for(int i = 0; i < shapes.size(); i++)
        if(shapes[i]->computeVolume() > current_max)
        {
            current_max = shapes[i]->computeVolume();
            maxV = shapes[i];
        }
    cout << "The shape with the largest volume is:"<<endl;
    maxV->display();
    cout << endl;
}
//  Purpose:  To read in a vector of pointers to shape objects and a factor to expand by, then the measurements of the shape objects are increased by a factor arguement.
//  Input: Vector of shape object pointers and factor to expand by
//  Output:  expands referenced objects by a factor specified by parameter.
void expandAll(vector<Shape*> shapes, int factor)
{
    if(factor > 0)
    {
        for(int i = 0; i < shapes.size(); i++)
            shapes[i]->expand(factor);
    }
}
/*
 Function: pause_215
 Purpose:  To pause the main function and wait for the user to hit enter
 Input:  Bool set to true
 Output:  Tells the user to click enter
 */
void pause_215(bool have_newline)
{
    // Prompt for the user to press ENTER, then wait for a newline.
    cout << endl << "Press ENTER to continue." << endl;
    cin.ignore(256, '\n');
}

int main(int argc, char* argv[])
{
    vector<Shape*> Shapes;
    // Check whether the user types the command correctly // if the number of command line arguments is not two
    if (argc != 2)
    {
        cout << "Invalid command!" << endl;
        cout << "Please enter the command: programname filename"<<endl;
        return 1;
    }
    // program continues...
    ifstream inData;
    // open the specified file for reading.
    // file name is provided by command line argument argv[1]
    inData.open(argv[1]);
    if (!inData.fail()) // if the file is opened successfully
    {
        string str;
        while(!inData.eof())
        {
            getline(inData, str, '\r');
            stringstream iss(str);
            string name;
            vector<double> measurements;
            iss >> name;
            double m1,m2,m3;
            iss >> m1>>m2>>m3;
            if(name == "Rectangle")
                Shapes.push_back(new Rectangle(m2, m1));
            if(name == "Circle")
                Shapes.push_back(new Circle(m1));
            if(name == "Cuboid")
                Shapes.push_back(new Cuboid(m2,m1,m3));
            if(name == "Sphere")
                Shapes.push_back(new Sphere(m1));
            if(name == "Cylinder")
                Shapes.push_back(new Cylinder(m1,m2));
            
        }
        inData.close();
        for(int i = 0; i < Shapes.size(); i++)
        {
            Shapes[i]->display();
            cout << endl;
        }
        maxSurfaceArea(Shapes);
        maxVolume(Shapes);
        int factor;
        bool goodinput = false;
        while (!goodinput)
        {
            cout << "Please input a positive integer as the factor to expand: " << endl;
           
            cin >> factor;
            cin.ignore(256, '\n');
            
            if (cin.fail() || factor <= 0)
            {
                if(cin.fail())
                    cout << "Invalid number!"<<endl;
                cout << "Please input a valid number greater than zero!"<<endl;
                cin.clear();
                cin.ignore(256, '\n');
            }
            else
            {
                goodinput = true;
                expandAll(Shapes, factor);
            }

        }
        for(int i = 0; i < Shapes.size(); i++)
        {
            Shapes[i]->display();
            cout << endl;
        }
        maxSurfaceArea(Shapes);
        maxVolume(Shapes);
    }
    
    for(int i = 0; i < Shapes.size(); i++)
    {
        delete Shapes[i];
    }
    
    //program continues...
    pause_215(true);
}
