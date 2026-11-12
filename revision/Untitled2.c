#include <stdio.h>

int sum(int i){
    i = i+1;
    return i;
    }
int main(){
    printf("%d\n",sum(5));
    printf("%d\n",sum(4));
    return 0;
    }
