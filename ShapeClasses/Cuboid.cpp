//
//  Cuboid.cpp
//  Program2
//
//  Created by Jesse Vaught on 11/11/16.
//  Copyright © 2016 Jesse Vaught. All rights reserved.
//

#include "Cuboid.h"
#include "Rectangle.h"
#include<cmath>
#include<iostream>
#include<iomanip>

using namespace std;

Cuboid::Cuboid()
{
    
}

Cuboid::Cuboid(double wid, double len, double hei) : Rectangle(wid, len)
{
    height = hei;
}

double Cuboid::computeArea()
{
    return (2*(getLength()*getWidth()) + 2*(getLength()*height) + 2*(getWidth()*height));
}

double Cuboid::computeVolume()
{
    double volume = getWidth() * getLength() * height;
    return volume;
}

void Cuboid::expand(int factor)
{
    setLength(getLength()*factor);
    setWidth(getWidth()*factor);
    height *= factor;
}
void Cuboid::display()
{
    cout << "Cuboid: (length = " << getLength() <<", width = "<< getWidth() << ", height = "<<height<<")" <<endl;
    cout << "The area is: " << computeArea() << endl;
    cout << "The volume is: " << computeVolume() << endl;
    
}
