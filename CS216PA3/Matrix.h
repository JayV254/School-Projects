//File: Matrix.h
//Purpose: to declare the template class Matrix
//Author: (your name)

#ifndef MATRIX_H
#define	MATRIX_H

#include <iostream>

using namespace std;

template <class T>
class Matrix
{
  public:
	Matrix(int sizeX, int sizeY, T initValue = T());
    Matrix(const Matrix &m);   // copy constructor 
	~Matrix();
	int GetSizeX() const { return dx; }
	int GetSizeY() const { return dy; }
    T& operator()(int x, int y);  // () operator overloading
    Matrix& operator=(const Matrix& m);  // = operator overloading
    //for friend functions, separate template declaration necessary
    template <class TYPE>
    friend ostream &operator<<(ostream &out, const Matrix<TYPE>& m); // << operator overloading
        
  private:
	T **p;       // pointer to a pointer to a T object
	int dx, dy;
};

#include "Matrix.cpp"
#endif	/* MATRIX_H */
