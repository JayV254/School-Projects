//
//  Cuboid.h
//  Program2
//
//  Created by Jesse Vaught on 11/9/16.
//  Copyright © 2016 Jesse Vaught. All rights reserved.
//
#include"Rectangle.h"

#ifndef Cuboid_h
#define Cuboid_h

class Cuboid : public Rectangle
{
public:
    Cuboid(); // default constructor
    Cuboid(double wid, double len, double hei);  // constructor based upon input
    double computeArea(); // compute area of cuboid
    double computeVolume(); // compute volume of cuboid
    void expand(int factor); // expand cuboid by a factor
    void display(); // hold values for area and volume and print them
private:
    double height;
};


#endif /* Cuboid_h */
