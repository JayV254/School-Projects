//
//  Rectangle.cpp
//  Program2
//
//  Created by Jesse Vaught on 11/11/16.
//  Copyright © 2016 Jesse Vaught. All rights reserved.
//

#include "Rectangle.h"
#include<cmath>
#include<iostream>
#include<iomanip>

using namespace std;

Rectangle::Rectangle()
{

}

Rectangle::Rectangle(double wid, double len)
{
    width = wid;
    length = len;
}

double Rectangle::getWidth()
{
    return width;
}

double Rectangle::getLength()
{
    return length;
    
}

void Rectangle::setWidth(double wid)
{
    width = wid;
}
void Rectangle::setLength(double len)
{
    length = len;
}

double Rectangle::computeArea()
{
    return (width*length);
}

double Rectangle::computeVolume()
{
    int volume = 0;
    return volume;
}

void Rectangle::expand(int factor)
{
    width *= factor;
    length *= factor;
}
void Rectangle::display()
{
    cout << "Rectangle: (length = " << length <<", width = "<< width<<")" <<endl;
    cout << "The area is: " << computeArea() << endl;
    cout << "The Volume is: " << 0 << endl;
    
}
