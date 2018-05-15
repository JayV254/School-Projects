#include <iostream>
#include <string>
#include <sstream>
#include <fstream>
#include <vector>
#include <cmath>
#include <iomanip>
#include <chrono>
#include <omp.h>
using namespace std;

double fx(double x, vector<double> coefficient, int degree) {
    
    double sum = 0;
    // iterate through polynomial of degree
    for(int i=degree; i >= 0; i--) {
        if(i > 0) {
            sum += (pow(x,i)) * coefficient[degree - i];
        } else {
            sum += coefficient[degree - i];
        }
    }
    return sum;
}

int main() {
    int degree;
    double  a, b, h, x,  sum=0, answer, N;
    vector <double> coeff;

    //set number of threads
    omp_set_num_threads(4);

    ifstream f;
    f.open("input.txt");
    if(f.fail()) {
        cout << "error reading input file" << endl;
    } else {
        string line;
        f >> degree >> ws;
        getline(f, line);
        istringstream ss(line);
        string token;

        int i = 0;

        //Separate string based on commas and white spaces
        while(getline(ss,token, ' ')) {
            size_t sz;
            //convert to double and push into vector
            coeff.push_back(stod(token.substr(sz)));
            i++;
        }
        // get upper and lower bounds
        f >> a >> b >> ws;
        // ask for N
        cout << "Please enter the desired number of subintervals: ";
        cin >> N;

        // intialize time value
        auto start_time = chrono::high_resolution_clock::now();

        // calculate h with upper, lower bound and subintervals
        h = fabs((b-a) / N);
        double chunk_sum = 0;
        #pragma omp parallel private(x,i,a,chunk_sum)
        #pragma omp for reduction ( + : sum )
        {
        // calculate sum of all values of x except upper and lower bound
            for(i=1; i<N; i++) {
                cout << omp_get_thread_num() << endl;
                x = a + (i*h);
                chunk_sum = fx(x, coeff, degree);
                // critical section
                #pragma omp critical
                    sum += chunk_sum;
            }
        }
        
        // calculate total integral with trapezoidal formula
        answer = (h/2) * (fx(a, coeff, degree) + fx(b, coeff, degree) + 
            (2*sum));
        // end time
        auto end_time = chrono::high_resolution_clock::now();
        auto time = end_time - start_time;

        cout << "Estimated integral value: " << fixed << setprecision(4) 
           << answer << endl;
        cout << "Total time calculating: " << 
           chrono::duration_cast<std::chrono::microseconds>(time).count() 
           << " microseconds" << endl;
    }
}

