#include <iostream>
#include <cstdio>
#include <cstdlib>
#include <cmath>
#include <algorithm>
#include <map>
#include <cstring>
#include <vector>
using namespace std;

struct area
{
    __int64 ans;
    int l, r;
};

map<int, int> keys;
map<int, int> keys2;

struct group
{
    vector<area*> arr;
};

bool cmp(area* a, area* b)
{
    if(a->r < b->r)
    {
        return true;
    }
    else
    if(a->r > b->r)
    {
        return false;
    }
    else return a->l < b->l;
}

__int64 initans(area a, int num[], int times[])
{
    __int64 ans = 0;
    for(int i = a.l; i <= a.r; i++)
    {
        if(!times[num[i]]++)
        {
            ans += keys2[num[i]];
        }
    }
    
    return ans;
}

int main()
{
    int n, tmp, q, l, r;
    int num[30005];
    int times[30005];
    int T;
    scanf("%d", &T);

    while(T--)
    {
        scanf("%d", &n);
        keys.clear();
        keys2.clear();
        for(int i = 0; i < n; i++)
        {
            scanf("%d", &tmp);
            if(keys.find(tmp) != keys.end())
            {
                num[i] = keys[tmp];
            }
            else
            {
                keys[tmp] = keys.size();
                keys2[keys[tmp]] = tmp;

                num[i] = keys[tmp];
            }
        }
        
        int glen = sqrt((double)n);
        int gcount = (n / glen) + (n % glen ? 1 : 0);
        
        group g[180];
        vector<area*> ansarr;
        
        scanf("%d", &q);
        area* atmp;
        for(int i = 0; i < q; i++)
        {
            scanf("%d%d", &l, &r);
            atmp = new area();
            atmp->l = l - 1, atmp->r = r - 1;

            int idx = (l - 1) / glen;
            g[idx].arr.push_back(atmp);
            ansarr.push_back(atmp);
        }
        
        for(int i = 0; i < gcount; i++)
        {
            sort(g[i].arr.begin(), g[i].arr.end(), cmp);
            
            if(g[i].arr.size() == 0) continue;
            
            memset(times, 0, sizeof(times));
            __int64 ans = g[i].arr[0]->ans = initans(*g[i].arr[0], num, times);
            
            for(int j = 1; j < g[i].arr.size(); j++)
            {
                if(g[i].arr[j]->l > g[i].arr[j - 1]->l)
                {
                    for(int k = g[i].arr[j - 1]->l; k < g[i].arr[j]->l; k++)
                    {
                        if(!--times[num[k]])
                        {
                            ans -= keys2[num[k]];
                        }
                    }
                }
                else
                if(g[i].arr[j]->l < g[i].arr[j - 1]->l)
                {
                    for(int k = g[i].arr[j]->l; k < g[i].arr[j - 1]->l; k++)
                    {
                        if(!times[num[k]]++)
                        {
                            ans += keys2[num[k]];
                        }
                    }
                }
                
                for(int k = g[i].arr[j - 1]->r + 1; k <= g[i].arr[j]->r; k++)
                {
                    if(!times[num[k]]++)
                    {
                        ans += keys2[num[k]];
                    }
                }
                
                g[i].arr[j]->ans = ans;
            }
        }
        
        for(int i = 0; i < ansarr.size(); i++)
        {
            printf("%I64d\n", ansarr[i]->ans);
        }

        for(int i = 0; i < ansarr.size(); i++) delete ansarr[i];
    }
    
    return 0;
}
