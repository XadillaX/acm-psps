#include <iostream>
#include <cstdio>
#include <cstdlib>
#include <cmath>
#include <string>
#include <map>
#include <algorithm>
using namespace std;

#define EXP 1e-8

map<string, bool> mp;

struct point {
    double x, y;
};

point operator + (point p1, point p2)
{
    p1.x += p2.x;
    p1.y += p2.y;
    return p1;
}

point operator - (point p1, point p2)
{
    p1.x -= p2.x;
    p1.y -= p2.y;
    return p1;
}

inline double disSquare(point& p1, point& p2)
{
    return (p1.x - p2.x) * (p1.x - p2.x) + (p1.y - p2.y) * (p1.y - p2.y);
}

inline bool eq(double a, double b)
{
    return fabs(a - b) < EXP;
}

bool cmp(double a, double b)
{
    return a < b;
}

inline int isRightAngled(point& p1, point& p2, point& p3)
{
    double d[3];
    d[0] = disSquare(p1, p2);
    d[1] = disSquare(p1, p3);
    d[2] = disSquare(p2, p3);
    sort(d, d + 3, cmp);
    
    if(eq(d[0] + d[1], d[2]))
    {
        if(eq(d[2], disSquare(p1, p2))) return 3;
        if(eq(d[2], disSquare(p1, p3))) return 2;
        if(eq(d[2], disSquare(p3, p2))) return 1;
    }
    
    return 0;
}

bool pointExists(point p)
{
    char tmp[64];
    sprintf(tmp, "%d,%d", (int)(p.x), (int)(p.y));
    
    return mp.end() != mp.find(tmp);
}

int main()
{
    int n;
    point p[105];
    while(~scanf("%d", &n))
    {
        mp.clear();
        
        for(int i = 0; i < n; i++)
        {
            scanf("%lf%lf", &(p[i].x), &(p[i].y));
            char tmp[64];
            sprintf(tmp, "%d,%d", (int)(p[i].x), (int)(p[i].y));
            mp[tmp] = true;
        }
        
        double ans = 0.0f;
        for(int i = 0; i < n; i++)
        {
            for(int j = i + 1; j < n; j++)
            {
                if(i == j) continue;
                for(int k = j + 1; k < n; k++)
                {
                    if(i == k || j == k) continue;
                    
                    int pn = isRightAngled(p[i], p[j], p[k]);
                    if(!pn) continue;
                    
                    //printf("Right Angled.\n");
                    
                    point ps[4];
                    pn--;
                    if(pn == 0) ps[0] = p[i], ps[1] = p[j], ps[2] = p[k];
                    else
                    if(pn == 1) ps[0] = p[j], ps[1] = p[i], ps[2] = p[k];
                    else
                    if(pn == 2) ps[0] = p[k], ps[1] = p[i], ps[2] = p[j];
                    
                    point d = ps[1] - ps[0];
                    ps[3] = ps[2] + d;
                    
                    //printf("(%d, %d) (%d, %d) (%d, %d) (%d, %d)\n", ps[0].x, ps[0].y, ps[1].x, ps[1].y, ps[3].x, ps[3].y, ps[2].x, ps[2].y);
                    
                    if(!pointExists(ps[3])) continue;
                    
                    //printf("(%.0lf, %.0lf) (%.0lf, %.0lf) (%.0lf, %.0lf) (%.0lf, %.0lf)\n", ps[0].x, ps[0].y, ps[1].x, ps[1].y, ps[3].x, ps[3].y, ps[2].x, ps[2].y);
                    
                    ans = max(ans, sqrt(disSquare(ps[0], ps[1])) * sqrt(disSquare(ps[0], ps[2])));
                }
            }
        }
        
        if(eq(ans, 0.0f))
        {
            printf("No Eyes\n");
        }
        else printf("%.4lf\n", ans);
    }
    
    return 0;
}
