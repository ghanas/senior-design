#include <cstdio>
#include <vector>
#include <iostream>
using namespace std;

int main()
{
  vector <string> lines;
  int i;
  string s;

  while (getline(cin, s)) lines.push_back(s);
  for (i = lines.size()-1; i >= 0; i--) cout << lines[i] << endl;
}
