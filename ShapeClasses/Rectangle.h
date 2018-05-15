//
//  Rectangle.h
//  Program2
//
//  Created by Jesse Vaught on 11/9/16.
//  Copyright © 2016 Jesse Vaught. All rights reserved.
//
#include"Shape.h"

#ifndef Rectangle_h
#define Rectangle_h

class Rectangle : public Shape
{
public:
    Rectangle(); // default constructor
    Rectangle(double wid, double len);  // constructor based upon input
    double computeArea(); // compute area of rectangle
    double computeVolume(); // set volume of rectangle equal to zero
    void expand(int factor); // expand rectangle by a factor
    void display(); // hold values for area and volume and prints them
    double getWidth();
    double getLength();
    void setWidth(double wid);
    void setLength(double len);
private:
    double width, length;
};


#endif /* Rectangle_h */
