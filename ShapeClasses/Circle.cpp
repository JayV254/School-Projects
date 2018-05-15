//
//  Circle.cpp
//  Program2
//
//  Created by Jesse Vaught on 11/11/16.
//  Copyright © 2016 Jesse Vaught. All rights reserved.
//

#include "Circle.h"
#include<iostream>
#include<cmath>
#include<iomanip>

using namespace std;

Circle::Circle()
{
    ;
}
double Circle::getRadius()
{
    return radius;
}
void Circle::setRadius(double r)
{
    radius = r;
}
Circle::Circle(double r)
{
    radius = r;
    
}

double Circle::computeArea()
{
    return pi*(radius*radius);
}

double Circle::computeVolume()
{
    int volume = 0;
    return volume;
}

void Circle::expand(int factor)
{
    radius = (radius*factor);
}
void Circle::display()
{
    cout << "Circle: (radius = " << radius << ")" <<endl;
    cout << "The area is: " << computeArea() << endl;
    cout << "The volume is: " << 0 << endl;
}


