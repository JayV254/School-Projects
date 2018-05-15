//
//  Cylinder.h
//  Program2
//
//  Created by Jesse Vaught on 11/9/16.
//  Copyright © 2016 Jesse Vaught. All rights reserved.
//
#include"Shape.h"
#include"Circle.h"

#ifndef Cylinder_h
#define Cylinder_h

class Cylinder : public Circle
{
public:
    Cylinder(); // default constructor
    Cylinder(double r, double hei);  // constructor based upon input
    double computeArea(); // compute area of cylinder
    double computeVolume(); // compute volume of cylinder
    void expand(int factor); // expand cylinder by a factor
    void display(); // hold values for area and volume
private:
    double height;
};


#endif /* Cylinder_h */
