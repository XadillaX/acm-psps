#include <iostream>
#include <cstdio>
#include <cstdlib>
using namespace std;

int main()
{
    int n, s;
    while(~scanf("%d%d", &n, &s))
    {
        int t = 0;
        double tmp;
        for(int i = 0; i < n; i++)
        {
            scanf("%lf", &tmp);
            
            int tmpint = (int)tmp;
            int r = tmpint % 10;
            
            if(r < 5) t++;
            else
            if(r == 5)
            {
                if(tmp - tmpint == 0) t++;
            }
        }
        
        printf("%.1lf\n", t * (60 + s * 0.2));
    }
    
    return 0;
}
