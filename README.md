acm-psps
========

ACM Problem Solution Publish System

WHAT'S ACM PSPS
---------------

You can see the effect here: http://acm.nbut.edu.cn/psps

This system is mainly for [ACM/ICPC](http://icpc.baylor.edu/) Online Judge System.

When you hold a contest on your Online Judge, you can publish the problem solution of this contest after it.

This system is based on an opensource PHP framework ***[ThinkPHP](https://github.com/liu21st/thinkphp) Release 3.1***. And the account service is based on [Douban](http://douban.com/). The comment service is [Denglu](https://denglu.cc).

HOW TO SET UP IT
----------------

For KISS rule, ***ACM PSPS*** is a non-database system. It's build up with only source file, PHP data cache file (indexes) and markdown file (content).

When you put this system on your sever, you have to modify the config file on `./src/Conf/config.php`:

> 1. You have assign the `MARKDOWN_PATH`, that is the abosolute path of `./markdown`
> 2. Douban API key is needed. You should go to the [Douban Developer Center](http://developers.douban.com/) to register for an application and get a API key. Then you should modify `DOUBAN_CLIENT_ID` and `DOUBAN_CLIENT_SECRET`.
> 3. Edit the `WEB_ROOT` and `BASE_HTTP` to your own path.

Next you should add yourself to the administrator group by modifing `./src/Conf/admin.php`.

> You can have several administrators. Just edit the `ADMIN` array.

Then you should edit your comment service. ACM PSPS is using ***denglu*** which is mentioned before. Go Denglu and register for an APPID.

Set up your denglu by editting `./src/Tpl/default/Contest/view.html`.

> Search for `YOURDENGLUAPPID` and replace it by your Denglu APPID.
>
> Of cause, you can register other comment service and replace it entirely.

After doing those things, you can run this website!

MANAGE
------

The manage entrance is http://yourdomain/pub/.

Login by Douban account that is declared in the administrator array. You will see the backend.

You should write the content by using `Markdown`.

CONTRIBUTION
------------

If you're interesting in this project, you can fork this. And if you have other ideas, please contact admin#xcoder.in.

Thx ^_^
