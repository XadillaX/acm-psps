#include <cstdio>
#include <cstdlib>
#include <iostream>
#include <algorithm>
#include <string>
#include <map>
#include <cstring>
using namespace std;

map<string, int> name;

int f[105];
bool vis[105];

int getfather(int i)
{
    if(f[i] != i) f[i] = getfather(f[i]);
    return f[i];
}

void uni(int a, int b)
{
    int fa = getfather(a), fb = getfather(b);
    if(fa != fb)
    {
        f[fa] = fb;
    }
}

int main()
{
    int n, m, tmp;
    char tmpname[25];
    while(~scanf("%d", &n))
    {
        name.clear();
        int ans = 0;
        for(int i = 0; i < n; i++)
        {
            scanf("%d", &m);
            string pre = "";
            if(0 == m) ans++;
            
            for(int j = 0; j < m; j++)
            {
                scanf("%s", tmpname);
                if(name.find(tmpname) == name.end())
                {
                    name[tmpname] = name.size() - 1;
                    f[name[tmpname]] = name[tmpname];
                }
                
                if(j != 0)
                {
                    int a = name[tmpname];
                    int b = name[pre];
                    
                    if(getfather(a) != getfather(b))
                    {
                        uni(a, b);
                    }
                }
                
                pre = tmpname;
            }
        }
        
        memset(vis, 0, sizeof(vis));
        for(int i = 0; i < name.size(); i++)
        {
            int fa = getfather(i);
            if(!vis[fa])
            {
                vis[fa] = true;
                ans++;
            }
        }
        
        if(name.size() != 0) ans--;
        printf("%d\n", ans);
    }

    return 0;
}
