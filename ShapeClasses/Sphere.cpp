//
//  Sphere.cpp
//  Program2
//
//  Created by Jesse Vaught on 11/11/16.
//  Copyright © 2016 Jesse Vaught. All rights reserved.
//

#include "Sphere.h"
#include<iostream>
#include<iomanip>

using namespace std;
Sphere::Sphere()
{
    ;
}

Sphere::Sphere(double r) : Circle(r)
{
    ;
}

double Sphere::computeArea()
{
    return (4*pi*(getRadius()*getRadius()));
}

double Sphere::computeVolume()
{
    return (4/(double)3)*pi*(getRadius()*getRadius()*getRadius());
}

void Sphere::expand(int factor)
{
    setRadius(getRadius()*factor);
}
void Sphere::display()
{
    cout << "Sphere: (radius = " << getRadius() << ")" <<endl;
    cout << "The area is: " << computeArea() << endl;
    cout << "The volume is: " << computeVolume() << endl;
    
}


