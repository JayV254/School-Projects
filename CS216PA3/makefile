OBJECTS = Main.o Graph.o Matrix.o
PA3: $(OBJECTS)
	g++ -std=c++11  $^ -o CS216PA3

HEADERS = Graph.h Matrix.h
%.o: %.cpp $(HEADERS)
	g++ -c -std=c++11 $< -o $@
clean:
	rm -f *.o CS216PA3
