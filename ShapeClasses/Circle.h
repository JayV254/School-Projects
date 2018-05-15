//
//  Circle.h
//  Program2
//
//  Created by Jesse Vaught on 11/9/16.
//  Copyright © 2016 Jesse Vaught. All rights reserved.
//
#include"Shape.h"

#ifndef Circle_h
#define Circle_h

class Circle : public Shape
{
public:
    Circle(); // default constructor
    Circle(double r);  // constructor based upon input
    double computeArea(); // compute area of circle
    double computeVolume(); // set volume of circle equal to zero
    void expand(int factor); // expand circle by a factor
    void display(); // hold values for area and volume
    double getRadius();
    void setRadius(double r);

    
private:
    double radius;
};

#endif /* Circle_h */
