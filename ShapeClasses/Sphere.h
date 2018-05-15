//
//  Sphere.h
//  Program2
//
//  Created by Jesse Vaught on 11/9/16.
//  Copyright © 2016 Jesse Vaught. All rights reserved.
//
#include"Shape.h"
#include"Circle.h"

#ifndef Sphere_h
#define Sphere_h

class Sphere : public Circle
{
public:
    Sphere(); // default constructor
    Sphere(double r);  // constructor based upon input
    double computeArea(); // compute surface area of sphere
    double computeVolume(); // compute volume of sphere
    void expand(int factor); // expand sphere by a factor
    void display(); // hold values for area and volume and print them
};


#endif /* Sphere_h */
