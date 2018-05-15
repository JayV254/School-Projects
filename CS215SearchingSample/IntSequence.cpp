//  CS215-004
//  IntSequence.cpp
//  Lab 12
//
//  Created by Jesse Vaught on 11/30/16.
//  Copyright © 2016 Jesse Vaught. All rights reserved.
//

#include "IntSequence.h"
#include <iostream>
#include <iomanip>
#include <cstdlib>
#include <vector>


IntSequence::IntSequence()

{
    
}

IntSequence::IntSequence(const IntSequence & other)
{
    for (int i = 0; i < other.getSize(); i++)
    {
        this->sequence[i] = other.sequence[i];
    }
    
}

IntSequence & IntSequence::operator=(const IntSequence & other)
{
    for (int i = 0; i < other.getSize(); i++)
    {
        this->sequence[i] = other.sequence[i];
    }
    return *this;
    
}

void IntSequence::insert(int item)
{
    sequence.push_back(item);
}

int IntSequence::getSize() const
{
    return sequence.size();
}

bool IntSequence::isEmpty() const
{
    if (getSize() == 0)
        return true;
    else
        return false;
}

void IntSequence::print() const
{
    if(getSize() > 0)
    {
        for (int i = 0; i < getSize(); i++)
            cout << sequence[i] << " ";
        cout << endl;
    }
    else
        cout << "The sequence is empty, you need to read data first..." << endl;
}

void IntSequence::bubble_sort()
{
    for (int i = 0; i < getSize(); i++)
    {
        cout << "Iteration " << i << ": ";
        print();
        for (int j = i + 1; j < getSize(); j++)
        {
            if (sequence[i] > sequence[j])
            {
                int temp = sequence[i]; //swap
                sequence[i] = sequence[j];
                sequence[j] = temp;
            }
        }
    }
    cout << "Sorted Sequence" << "    ";
    print();
    cout << endl;
}

int IntSequence::binary_search_helper(int key, int leftindex, int rightindex)
{
    //if (leftindex > rightindex)
    if (leftindex > rightindex)
        return -1;
    else
    {
        int middle = (leftindex + rightindex) / 2;
        
        if (sequence[middle] > key)
            return binary_search_helper(key, leftindex, middle - 1);
        
        else if (sequence[middle] < key)
            return binary_search_helper(key, middle + 1, rightindex);
        
        else
            return middle;
        
    }
}

int IntSequence::binary_search(int key)
{
    return binary_search_helper(key, 0, getSize() - 1);
}

int IntSequence::sequential_search(int key) const
{
    int flag;
    int index_found = 0;
    for(int i=0; i<10; i++)    // start to loop through the array
    {
        if (sequence[i] == key)   // if match is found
        {
            flag = true;
            index_found = i;// turn flag on
            break ;    // break out of for loop
        }
    }
    if (flag)    // if flag is TRUE (1)
    {
        return index_found;
    }
    else
    {
        return -1;
    }
}

void IntSequence::shuffle()
{
    random_shuffle(sequence.begin(), sequence.end());
}

IntSequence::~IntSequence()
{
    
}

void IntSequence::selection_sort()
{
    // Step through each element of the vector <int> sequence
    for (int startIndex = 0; startIndex < getSize(); startIndex++)
    {
        // smallestIndex is the index of the smallest element so far.
        int smallestIndex = startIndex;
        
        // Look for smallest element remaining in the array (starting at startIndex+1)
        for (int currentIndex = startIndex + 1; currentIndex < getSize(); currentIndex++)
        {
            // If the current element is smaller than our previously found smallest
            if (sequence[currentIndex] < sequence[smallestIndex])
                // This is the new smallest number for this iteration
                smallestIndex = currentIndex;
        }
        cout << "Min " << sequence[startIndex] << "," << "swap with " <<sequence[startIndex] << ":" << endl;
        // Swap our start element with our smallest element
        swap(sequence[startIndex], sequence[smallestIndex]);
    }
    cout << "Sorted sequence    ";
    print();
}

void IntSequence::clear()
{
    sequence.clear();
}
