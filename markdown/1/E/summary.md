　　题目就是给你一坨点，然后你找出这些点构成的矩形中最大的那个矩形的面积。注意这个矩形是可以斜着的。

　　方法很多，这边标程的做法就是三重循环，枚举三个点，看这三个点是否能构成直角三角形（勾股定理）。若是直角三角形则再补上第四个顶点的坐标形成一个矩形，然后判断这个坐标存不存在给出的坐标中。若存在则这是一个矩形。

　　最后把所有矩形的面积比较一下拿出最大那个矩形即可。