//
//  File: Shape.h
//  Program2
//  CS215-003
//  Created by Jesse Vaught on 11/7/16.
//  Purpose: To define the "Shape" base class

#ifndef Shape_h
#define Shape_h

class Shape
{
public:
    Shape() {}
    virtual double computeArea() = 0;
    virtual double computeVolume() = 0;
    virtual void expand(int factor) = 0;
    virtual void display() = 0;
    virtual ~Shape() {}
    const double pi = 3.14159265359;
};

#endif /* Shape_h */
