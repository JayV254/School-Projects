//
//  Cylinder.cpp
//  Program2
//
//  Created by Jesse Vaught on 11/11/16.
//  Copyright © 2016 Jesse Vaught. All rights reserved.
//

#include "Cylinder.h"
#include "Circle.h"
#include<cmath>
#include<iostream>
#include<iomanip>

using namespace std;

Cylinder::Cylinder()
{
    ;
}

Cylinder::Cylinder(double radius, double hei) : Circle(radius)
{
    height = hei;
}

double Cylinder::computeArea()
{
    return ((pi * (getRadius() * 2) * height)) + 2*(pi*(getRadius()*getRadius()));
}

double Cylinder::computeVolume()
{
    double volume = pi*(getRadius()*getRadius())*height;
    return volume;
}

void Cylinder::expand(int factor)
{
    setRadius(getRadius() * factor);
    height *= factor;
}
void Cylinder::display()
{
    cout << "Cylinder: (radius = " << getRadius() <<", height = "<<height<< ")" <<endl;
    cout << "The area is: " << computeArea() << endl;
    cout << "The volume is: " << computeVolume() << endl;
    
}


