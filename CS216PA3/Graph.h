// Declaration of Graph class
// This class represents a undirected graph
// using adjacent matrix representation
#ifndef GRAPH_H
#define GRAPH_H

#include <iostream>
#include <vector>
#include "Matrix.h"
 
using namespace std;
 
class Graph
{
    private:    
        Matrix<int> adj;        // using adjancy matrix representation
    public:
        Graph(int numVertices);  // Constructor
        bool hasEdge(int v, int w);  // to check if an edge exists
        void addEdge(int v, int w, int edge); // function to add an edge to graph
        int getEdge(int v, int w);  // to return the edge from v to w
        // Apply BFS traversal to find the shortest path from the given source s
        // store the shortest path distance from the given source s in distance vector
        // store the next vertex on the shortest path back to the source s in go_through vector
        void BFS(int s, vector<int>& distance, vector<int>& go_through);
};

#endif   /* GRAPH_H */
