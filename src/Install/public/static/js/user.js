function useUtilsTabs(dom,className,cheep = false,object=(key,open)=>{}){
    const nodeList = document.querySelectorAll(dom); // 获取菜单元素
    for(let i = 0; i <  nodeList.length; i++){
        nodeList[i].querySelector('a').onclick = function (){
            $key = this.parentNode.getAttribute('data-key');
            if(cheep){
                this.parentNode.classList.add(className);
                for (let j = 0; j < nodeList.length; j++){
                    if(nodeList[j] !== this){
                        nodeList[j].classList.remove(className);
                    }
                }
                object($key,true)
            }else{
                if(this.parentNode.classList.contains(className)){
                    this.parentNode.classList.remove(className);
                    object($key,false)
                }else{
                    this.parentNode.classList.add(className);
                    object($key,true)
                }
            }

        }
    }
}