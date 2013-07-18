#include <iostream>
#include <cstdio>
#include <cstdlib>
#include <cstring>
using namespace std;

int n;
int M[5][100005];
int dp[100005][5][2];

int main()
{
    while(~scanf("%d", &n))
    {
        for(int i = 0; i < 5; i++)
        {
            for(int j = 0; j < n; j++)
            {
                scanf("%d", M[i] + j);
            }
        }
        
        memset(dp, 0, sizeof(dp));
        dp[0][0][0] = M[0][0];
        dp[0][1][0] = M[1][0];
        dp[0][2][0] = M[2][0];
        dp[0][3][0] = M[3][0];
        dp[0][4][0] = M[4][0];
        
        for(int i = 0; i < 5; i++)
        {
            for(int j = 0; j < 5; j++)
            {
                if(i == j) continue;
                
                if(i < j) dp[1][i][0] = max(dp[1][i][0], dp[0][j][0] + M[i][1]);
                else
                if(i > j) dp[1][i][1] = max(dp[1][i][1], dp[0][j][0] + M[i][1]);
            }
        }
        
        int ans = 0;
        for(int i = 2; i < n; i++)
        {
            for(int j = 0; j < 5; j++)
            {
                for(int k = 0; k < 5; k++)
                {
                    if(j == k) continue;
                    
                    if(j < k) dp[i][j][0] = max(dp[i][j][0], dp[i - 1][k][1] + M[j][i]);
                    else
                    if(j > k) dp[i][j][1] = max(dp[i][j][1], dp[i - 1][k][0] + M[j][i]);
                }
                
                if(i == n - 1)
                {
                    ans = max(max(ans, dp[i][j][0]), dp[i][j][1]);
                }
            }
        }
        
        printf("%d\n", ans);
    }
    
    return 0;
}
