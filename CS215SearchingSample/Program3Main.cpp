//  Program 3
//  CS215-004
//
//  Created by Jesse Vaught on 12/2/16.
//  Copyright © 2016 Jesse Vaught. All rights reserved.

//  Purpose:  To allow user to input a sequence of numbers, and then search/print that sequence through a menu interface.
//  Input:  User is allowed to choose "read" option in order to input individual elements in a sequence
//  Output:  The program will output the sequence at different points of the program.  If the sequence is changed in any way the change is outputted to the screen.  If the user wants to print the sequence at any given time the print option of the main menu will allow them to do so.
//

#include "IntSequence.h"
#include <iostream>
#include <cstdlib>

using namespace std;

//  Function: readin_seq
//  Purpose:  allows user to input individual elements into a sequence
//  Input:  integer elements into sequence
//  Output:  no output

void readin_seq(IntSequence& sequence)
{
    int element;
    if(!sequence.isEmpty())
        sequence.clear();
    while (true)
    {
        // Ask the user for the size of the sequence until the user enters "Q" to quit
        cout << "Enter the next element (Enter 'q' to stop):";
        cin >> element;
        cin.ignore(256, '\n');
        
        if (cin.fail())
        {
            cin.clear();
            string input_to_check;
            cin >> input_to_check;
            if (input_to_check == "Q" || input_to_check == "q")
                break;
            cout << "Invalid number!" << endl;
            continue;
        }
        else
            sequence.insert(element);
    }
}

//  Function: key_input
//  Purpose:  allows user to input a key to search for in the sequence, and validates that input
//  Input:  key to search for
//  Output:  no output

int key_input()
{
    int key;
    
    // Ask the user for the key to find
    cout << "Enter the key to find: ";
    cin >> key;
    cin.ignore(256, '\n');
        
    if (cin.fail())
    {
        cin.clear();
        cout << "Invalid Searching Key..." << endl;
        key = -1;
    }
    return key;
}

//  Function: search
//  Purpose:  displays search menu and search submenu to allow user to search for a specific key in the sequence created
//  Input: key input provided by other function, menu option is inputted and validated by user
//  Output:  outputs certain search iterations to allow user to see how that sort works.  Outputs whether or not the key was found, and if it was found it outputs the index where it was found.

void search(IntSequence& sequence)
{
    int option_menu_1;
    bool search = true;
    while(option_menu_1 != 3)
    {
        if(sequence.getSize() <= 0)
        {
            cout << "Cannot search empty sequence!"<<endl;
            break;
        }
        cout <<"Please choose from the following sub-menu:"<<endl<< "1.Sequential search" << endl<< "2.Binary Search" << endl << "3.Quit Search" << endl;
        cout << "Search option: ";
        cin >> option_menu_1;
        cin.ignore(256, '\n');
        if(cin.fail() || ((0 >(3-option_menu_1) || (3-option_menu_1 > 3))))
        {
            if(cin.fail())
            {
                cin.clear();
                cin.ignore(256, '\n');
            }
            cout << "Invalid Searching Option...." <<endl;
        }
        else
        {
            int key_to_find;
            switch (option_menu_1)
            {
                //Sequential Search
                case 1:
                    int index;
                    key_to_find = key_input();
                    if(key_to_find != -1)
                    {
                        index = sequence.sequential_search(key_to_find);
                        cout << "Key found at index " << index << endl;
                    }
                    continue;
                //Binary Search
                
                case 2:
                    key_to_find = key_input();
                    int option_menu_2;
                    cout << "Unsorted Sequence    ";
                    sequence.print();
                    cout <<"Please choose one of the following algorithms to sort the sequence:"<<endl << "1. Bubble Sort" <<endl << "2. Selection Sort" << endl;
                    cin >> option_menu_2;
                    cin.ignore(256, '\n');
                
                    if (cin.fail() || (((2-option_menu_2) < 0) || (2-option_menu_2) > 2))
                    {
                        cin.clear();
                        cout << "Invalid Option!" << endl << "The sequence is not sorted, cannot apply binary search!" << endl;
                        search = false;
                    }
                    else
                        switch (option_menu_2)
                    {
                        //Bubble Sort
                        case 1: sequence.bubble_sort();
                            break;
                        case 2: sequence.selection_sort();
                            break;
                        
                    }
                    if(search)
                    {
                        int index = sequence.binary_search(key_to_find);
                        if (index == -1)
                            cout << "Key not Found" << endl;
                        else
                            cout << "Key found at index " << index << endl;
                        sequence.shuffle();
                        cout << "After calling shuffle, the sequence is in a random permutation:" <<endl<< "Sequence    ";
                        sequence.print();
                    }
                    break;
                
                case 3:
                    break;
            }
        }
    }
}

//  Function: pause_215
//  Purpose:  pauses and waits for user to hit enter
//  Input:  enter key
//  Output: tells user to hit enter to continue
void pause_215(bool have_newline)
{
    if (have_newline)
    {
        // Ignore the newline after the user's previous input.
        cin.ignore(256, '\n');
    }
    
    // Prompt for the user to press ENTER, then wait for a newline.
    cout << endl << "Press ENTER to continue." << endl;
    cin.ignore(256, '\n');
}

//  Function: main
//  Purpose:  driver function to control relationship between object class and menus that morph or act on the objects member/private functions and data members.
//  Input:  main option menu choice
//  Output:  lets user know if menu option was invalid for main menu, outputs a thank you message to user for participating in the program
int main()
{
    IntSequence User_seq;
    int option = 0;
    while(option != 4)
    {
        cout << "=============================================" << endl;
        cout << "1. Read" << endl<< "2. Print" << endl << "3. Search" << endl << "4. Quit"<< endl;
        cout << "Option: ";
        cin >> option;
        if((option <= 4) && (option > 0))
        {
            if(option == 1)
                readin_seq(User_seq);
            if(option ==2)
                User_seq.print();
            if(option == 3)
                search(User_seq);
            if(option == 4)
                break;
        }
        else
        {
            cout << "Invalid Option!" << endl;
            cin.clear();
            cin.ignore(256, '\n');
        }
    }
    cout << "Thank you for using this program!";
    pause_215(true);
    return 0;
}
