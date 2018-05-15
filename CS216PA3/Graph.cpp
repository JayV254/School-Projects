// It provides the implementation of the Graph class
#include <list>     // create a queue from a list
#include <queue>
#include <cassert>
#include <set>
#include <list>
#include "Graph.h"

// default constructor
Graph::Graph(int numVertices):adj(Matrix<int>(numVertices, numVertices, -1))
{
}

bool Graph::hasEdge(int v, int w)
{
    assert(v>=0 && v < adj.GetSizeX() && w >=0 && w < adj.GetSizeX());
    if (adj(v, w)==-1)
        return false;
    return true;
}

// Please provide your implementation
// for the following three member functions
void Graph::addEdge(int v, int w, int edge)
{
    adj(v,w) = edge;
    adj(w,v) = edge;
}

int Graph::getEdge(int v, int w)
{
    return adj(v,w);
}

void Graph::BFS(int s, vector<int>& distance, vector<int>& go_through)
{
    int Vertices = distance.size();
    bool *visited = new bool[Vertices];
    for(int i=0; i < Vertices; i++)
    {
        visited[i] = false;
    }
    
    list <int> queue;
    visited[s] = true;
    distance[s] = 0;
    queue.push_back(s);
    go_through[s] = 0;
    while(!queue.empty())
    {
        int current = queue.front();
        queue.pop_front();
        for(int n = 0; n< distance.size(); n++)
        {
            if(!visited[n] && adj(current, n) != -1)
            {
                visited[n] = true;
                queue.push_back(n);
                go_through[n] = current;
                distance[n] = distance[current] + 1;
            }
        }
    }
}
