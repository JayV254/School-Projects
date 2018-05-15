// It provides the definition of the template class Matrix
// Since it is a template class, we need to add "include guard" for .cpp file

#ifndef MATRIX_CPP
#define MATRIX_CPP

#include <iostream>
#include <cassert>
#include "Matrix.h"

using namespace std;

template <class T>
Matrix<T>::Matrix(int sizeX, int sizeY, T initValue) : dx(sizeX), dy(sizeY)
{
	assert(sizeX > 0 && sizeY > 0);
	p = new T*[dx];		// create array of pointers to T items
	assert(p != 0);
	for (int i = 0; i < dx; i++)
	{	  // for each pointer, create array of T itmes
		p[i] = new T[dy];  
		assert(p[i] != 0);
		for (int j = 0; j < dy; j++)
			p[i][j] = initValue;
	}
}

template <class T>
Matrix<T>::Matrix(const Matrix<T> &m) : dx(m.dx), dy(m.dy)
{
	p = new T*[dx];            // create array of pointers to T items
	assert(p != 0);
	for (int i = 0; i < dx; i++)
	{
		p[i] = new T[dy];  // for each pointer, create array of T items
		assert(p[i] != 0);
		for (int j = 0; j < dy; j++)
			p[i][j] = m.p[i][j];
	}
}

template <class T>
Matrix<T>::~Matrix()
{
	for (int i = 0; i < dx; i++)
		delete [] p[i];	// delete arrays of T items
	delete [] p;	// delete array of pointers to T
}

template <class T>
Matrix<T> &Matrix<T>::operator=(const Matrix<T> &m)
{
	if (this != &m)
	{
		assert(dx == m.dx && dy == m.dy);
		for (int i = 0; i < dx; i++)
			for (int j = 0; j < dy; j++)
				p[i][j] = m.p[i][j];
	}
	return *this;
}

template <class T>
T &Matrix<T>::operator()(int x, int y)
{
    assert(x >= 0 && x < dx && y >= 0 && y < dy);
    return p[x][y];
}

template <class T>
ostream &operator<<(ostream &out, const Matrix<T> &m)
{
    out << endl;
    for (int x = 0; x < m.dx; x++)
    {
	    for (int y = 0; y < m.dy; y++)
            out << m.p[x][y] << "\t";
        out << endl;
    }
    return out;
}

#endif  /* MATRIX_CPP */
