
function domGetCookie(cname){
    var name = cname + "=";
    var ca = document.cookie.split(';');
    for(var i=0; i<ca.length; i++)
    {
        var c = ca[i].trim();
        if (c.indexOf(name)===0) return c.substring(name.length,c.length);
    }
    return "";
}
function domSetCookie(key,value,expireDays){
    let date = new Date();
    date.setDate(date.getDate() + expireDays);
    document.cookie = key + '=' + encodeURI(value) + ((expireDays==null)?";path=/":";expires="+date.toUTCString()+";path=/");
}
function domDelCookie(name){
    let exp = new Date();
    exp.setTime(exp.getTime() - 1);
    let val = domGetCookie(name);
    if(val != null){
        document.cookie = name + '=' + val + ";expires="+ exp.toUTCString()+";path=/";
    }
}

function useUtilsIsArray(val){
    return val && Array.isArray(val);
}

function useUtilsConvertTreeData(data){
    if(data !== undefined && data.length !== 0) {
        for (let i = 0; i < data.length; i++) {
            if (data[i].children !== undefined) {
                const temp = data[i].children;
                delete data[i].children;
                data = data.concat(temp);
            }
        }
    }
    return data;
}
function useUtilsTopTreeNodes(list,keyCol,keyVal){
	let newList = [];
	list.forEach(item=>{
		if(item.children !== undefined && item.children.length !== 0){
			let leaf = useUtilsTopTreeNodes(item.children,keyCol,keyVal);
			if(leaf !== undefined){
				item.children = leaf;
				newList.push(item);
			}else{
				if(item[keyCol] === keyVal){
					//    delete item['children'];
					newList.push(item);
				}
			}
		}else{
			if(item[keyCol] === keyVal){
				newList.push(item);
			}
		}
	});
	if(newList != undefined && newList.length != 0){
		return newList;
	}
}
function useUtilsDescartes(array){
    if( array.length < 2 ) return array[0] || [];
    return array.reduce((col,set)=>{
        let res = [];
        col.forEach(function(c) {
            set.forEach(function(s) {
                if(c instanceof Array){
                    res.push([...c,s]);
                }else{
                    res.push([c,s]);
                }

            })});
        return res;
    })
}
function wwiPriceFormat(price,prefix = '￥'){
	return wwi.priceFormat(price,prefix = '￥');
}

(
	wwi = {

	/**
	 * 判断是否是数组
	 * @param {Object} obj
	 */
	isArray: function (obj) {
	    return Array.isArray(obj);
	},
	/**
	 * 判断是否是对象
	 * @param {Object} obj
	 */
	isObject: function (obj) {
		var type = typeof obj;
		return type === 'function' || type === 'object' && !!obj;
	},
	/**
	 * 是否为空
	 * @param {Object} obj
	 */
	isEmpty: function (obj) {
	        if (wwi.isValidate('Boolean',obj)) return !obj;
	        if (obj == null) return true;
	        if (wwi.isValidate('Number',obj)) {
	            return obj <= 0;
	        }
	        if(wwi.isArray(obj) && obj.length ==1 )return wwi.isEmpty(obj[0])
	        if (wwi.isArray(obj) || wwi.isValidate('String',obj) || (obj instanceof jQuery)) return obj.length === 0;
	        return Object.keys(obj).length === 0;
	},
	/**
	 * 验证类型 'Function', 'String', 'Number', 'Date', 'Boolean'
	 * @param {Object} type
	 * @param {Object} obj
	 */
	isValidate:function(type,obj){
		return Object.prototype.toString.call(obj) === '[object ' + type + ']';
	},
	/**
	 * 数据去重
	 * @param {Object} arr
	 * @param {Object} name
	 */
	unique: function(arr,name = 'id'){
		return arr.filter((value, index, self) => {
			return self.findIndex(t => t[name] === value[name]) === index;
		})
	},
	/**
	 * 合并数组并去重
	 */
	concatUnique: function(arrA=[],arrB=[],name='id'){
		const mergeData = arrA.concat(arrB);
		return mergeData.reduce((cur,next)=>{
			const repeat = cur.some((item)=>{
				return item[name] === next[name]
			});
			if(!repeat){
				return cur.concat(next);
			}else{
				return cur;
			}
		},[])
	},
	sortArrayAsc:function(arr,name){
		return arr.sort((a,b)=>{
			if (a[name] < b[name]) return -1;
			if (a[name] > b[name]) return 1;
			return 0;
		});
	},
	sortArrayDesc:function(arr,name){
		return arr.sort((a,b)=>{
			if (a[name] > b[name]) return -1;
			if (a[name] < b[name]) return 1;
			return 0;
		});
	},
	parserTimeAgo(timestamp){
		const now = new Date();
		const inputTime = new Date(timestamp);
		const diffInSeconds = Math.floor((now - inputTime) / 1000);

		if (diffInSeconds <= 60) {
			return `${diffInSeconds}秒前`;
		} else if (diffInSeconds > 60 && diffInSeconds <= 3600) {
			const minutes = Math.round(diffInSeconds / 60);
			return `${minutes}分钟前`;
		} else if (diffInSeconds > 3600 && diffInSeconds <= 86400) {
			const hours = Math.round(diffInSeconds / 3600);
			return `${hours}小时前`;
		} else {
			const days = Math.ceil(diffInSeconds / 86400);
			return `${days}天前`;
		}
	},
	priceFormat: function(price,prefix = '￥'){
	    return '<span class="yuan">'+ prefix +'</span>' + wwi.numberFormatPrice(price,2);
	},
	numberFormatPrice: function(num, ext){
	    if(ext < 0){
	        return num;
	    }
	    num = Number(num);
	    if(isNaN(num)){
	        num = 0;
	    }
	    var _str = num.toString();
	    var _arr = _str.split('.');
	    var _int = _arr[0];
	    var _flt = _arr[1];
	    if(_str.indexOf('.') == -1){
	        if(ext == 0){
	            return '<span class="integer">' +_str+ '</span>';
	        }
	        var _tmp = '';
	        for(var i = 0; i < ext; i++){
	            _tmp += '0';
	        }
	        _str = _str + '.' + _tmp;
	    }else{
	        if(_flt.length == ext){
	            return '<span class="integer">'+_int+'</span><span class="pointer">.</span><span class="decimal">' +_flt+ '</span>';
	        }
	        if(_flt.length > ext){
	            _str = _str.substr(0, _str.length - (_flt.length - ext));
	            if(ext == 0){
	                _str = _int;
	            }
	        }else{
	            for(var i = 0; i < ext - _flt.length; i++){
	                _str += '0';
	            }
	        }
	    }
	    var _arrTwo = _str.split('.');
	    var _intTwo = _arrTwo[0];
	    var _fltTwo = _arrTwo[1];
	    return '<span class="integer">'+_intTwo+'</span><span class="pointer">.</span><span class="decimal">' +_fltTwo+ '</span>';
	},
	imTalk:function(sid){
		return window.open(shopwwiSiteUrl + '/chat?sid=' + sid,'chatWindow','width=1200,height=1000');
	},
	number:{
		/**
		 * 四舍五入
		 * @param number 数字
		 * @param fractionDigits 保留小数位数
		 * @returns {number}
		 */
		round: function (number, fractionDigits) {
		    return Math.round(number * Math.pow(10, fractionDigits)) / Math.pow(10, fractionDigits);
		},
		
		/**
		 * Copyright: Bizpower多用户商城系统
		 * 加法运算
		 */
		add: function (num1, num2) {
		    var baseNum, baseNum1, baseNum2;
		    num1 = parseFloat(num1, 10);
		    num2 = parseFloat(num2, 10);
		    try {
		        baseNum1 = num1.toString().split(".")[1].length;
		    } catch (e) {
		        baseNum1 = 0;
		    }
		    try {
		        baseNum2 = num2.toString().split(".")[1].length;
		    } catch (e) {
		        baseNum2 = 0;
		    }
		    baseNum = Math.pow(10, Math.max(baseNum1, baseNum2));
		    return (num1 * baseNum + num2 * baseNum) / baseNum;
		},
		/**
		 * 减法运算
		 */
		sub: function (num1, num2) {
		    var baseNum, baseNum1, baseNum2;
		    var precision; // 精度
		    num1 = parseFloat(num1, 10);
		    num2 = parseFloat(num2, 10);
		    try {
		        baseNum1 = num1.toString().split(".")[1].length;
		    } catch (e) {
		        baseNum1 = 0;
		    }
		    try {
		        baseNum2 = num2.toString().split(".")[1].length;
		    } catch (e) {
		        baseNum2 = 0;
		    }
		    baseNum = Math.pow(10, Math.max(baseNum1, baseNum2));
		    precision = (baseNum1 >= baseNum2) ? baseNum1 : baseNum2;
		    return ((num1 * baseNum - num2 * baseNum) / baseNum).toFixed(precision);
		},
		/**
		 * 乘法运算
		 */
		mul: function (num1, num2) {
		    var baseNum = 0;
		    num1 = parseFloat(num1, 10);
		    num2 = parseFloat(num2, 10);
		    try {
		        baseNum += num1.toString().split(".")[1].length;
		    } catch (e) {
		    }
		    try {
		        baseNum += num2.toString().split(".")[1].length;
		    } catch (e) {
		    }
		    return Number(num1.toString().replace(".", "")) * Number(num2.toString().replace(".", "")) / Math.pow(10, baseNum);
		},
		/**
		 * 除法运算
		 */
		div: function (num1, num2) {
		    var baseNum1 = 0,
		        baseNum2 = 0;
		    num1 = parseFloat(num1, 10);
		    num2 = parseFloat(num2, 10);
		    var baseNum3, baseNum4;
		    try {
		        baseNum1 = num1.toString().split(".")[1].length;
		    } catch (e) {
		        baseNum1 = 0;
		    }
		    try {
		        baseNum2 = num2.toString().split(".")[1].length;
		    } catch (e) {
		        baseNum2 = 0;
		    }
		    with (Math) {
		        baseNum3 = Number(num1.toString().replace(".", ""));
		        baseNum4 = Number(num2.toString().replace(".", ""));
		        return (baseNum3 / baseNum4) * pow(10, baseNum2 - baseNum1);
		    }
		},
		/**
		 * Copyright: BIZPOWER
		 * 数组内数字相乘
		 * @param  {[type]} arr [description]
		 * @return {[type]}     [description]
		 */
		mMulti: function mMulti(arr) {
		    var r = 0;
		    if (Nc.isArray(arr)) {
		        arr.forEach(function (n) {
		            var _t = parseFloat(n);
		            if (!isNaN(_t)) {
		                r = r === 0 ? n : wwi.number.mul(n, r);
		            }
		        });
		    } else {
		        for (var i = 0; i < arguments.length; i++) {
		            var _t = parseFloat(arguments[i]);
		            if (!isNaN(_t)) {
		                r = r === 0 ? _t : wwi.number.mul(_t, r);
		            }
		
		        }
		    }
		    return r;
		},
		format: function (num, ext){
		    if (ext < 0) {
		        return num;
		    }
		    num = Number(num);
		    if (isNaN(num)) {
		        num = 0;
		    }
		    var _str = num.toString();
		    var _arr = _str.split('.');
		    var _int = _arr[0];
		    var _flt = _arr[1];
		    if (_str.indexOf('.') === -1) {
		        /* 鎵句笉鍒板皬鏁扮偣锛屽垯娣诲姞 */
		        if (ext == 0) {
		            return _str;
		        }
		        var _tmp = '';
		        for (var i = 0; i < ext; i++) {
		            _tmp += '0';
		        }
		        _str = _str + '.' + _tmp;
		    } else {
		        if (_flt.length == ext) {
		            return _str;
		        }
		        /* 鎵惧緱鍒板皬鏁扮偣锛屽垯鎴彇 */
		        if (_flt.length > ext) {
		            _str = _str.substr(0, _str.length - (_flt.length - ext));
		            if (ext == 0) {
		                _str = _int;
		            }
		        } else {
		            for (var i = 0; i < ext - _flt.length; i++) {
		                _str += '0';
		            }
		        }
		    }
		    return _str;
		}
	},
});

const WwiAmis = {
    template:`<div id="amis-region" class="relative"></div>`,
    props:['schema'],
    mounted(){
        this.init()
    },
    methods:{
        init(){
            const amis = amisRequire("amis/embed");
            amis.embed("#amis-region", this.schema)
        }
    },
    watch:{
        schema(newVal,oldVal){
            this.init()
        }
    }
}
const WwiCountDown = {
    template:`<div>
        <slot name="default" :row="time_data" :finish="isfinish">
            {{ text }}
        </slot>
    </div>`,
    data(){
        return {
            now:0,
            isfinish:true,
            countDownId: undefined
        }
    },
    emits:['start', 'end', 'change'],
    props:{
        time: {
            type: Number,
            default: 10 * 1000
        },
        /**
         * 取值的格式字符串
         * 注意如果当你的formatType设定某值时，里面只能读取到你设定的值。
         * */
        format: {
            type: String,
            default: 'DD天HH小时MM分SS秒MS毫秒'
        },
        /**
         * 到计时格式的类型，设定下面的值时，倒计时功能不会进位，而是以指定的值进行倒计时。
         * 比如分，你设置为MM,那么你到计时如果是200分钟，就从200开始倒计时。而不会进位到小时。
         * "DD"|"HH"|"MM"|"SS"|"MS"|""
         * 天|时|分|秒|毫秒
         * */
        formatType:{
            type:String,
            default:""
        },
        autoStart: {
            type: Boolean,
            default: true
        },
        color: {
            type: String,
            default: ''
        }
    },
    computed:{
        time_data(){
            return this.formatTime(this.time - this.now);
        },
        text(){
            let ps = this.format

            if(!this.formatType){
                ps = ps.replace(/(DD)/g, String(this.time_data.day))
                ps = ps.replace(/(MM)/g, String(this.time_data.minutes))
                ps = ps.replace(/(HH)/g, String(this.time_data.hour))
                ps = ps.replace(/(SS)/g, String(this.time_data.seconds))
                ps = ps.replace(/(MS)/g, String(this.time_data.millisecond))
            }else{
                if(this.formatType=="DD"){
                    ps = ps.replace(/(DD)/g, String(this.time_data.DD))
                }
                if(this.formatType=="HH"){
                    ps = ps.replace(/(HH)/g, String(this.time_data.HH))
                }
                if(this.formatType=="MM"){
                    ps = ps.replace(/(MM)/g, String(this.time_data.MM))
                }
                if(this.formatType=="SS"){
                    ps = ps.replace(/(SS)/g, String(this.time_data.SS))
                }
                if(this.formatType=="MS"){
                    ps = ps.replace(/(MS)/g, String(this.time_data.MS))
                }
            }
            return ps
        }
    },
    mounted(){
        this.formatTime(this.time)
        if (this.autoStart) {
            this.start()
        }
    },
    methods:{
        formatTime(my_time){
            const daysRound = Math.floor(my_time / 1000 / 60 / 60 / 24)
            const hoursRound = Math.floor((my_time / 1000 / 60 / 60) % 24)
            const minutesRound = Math.floor((my_time / 1000 / 60) % 60)
            const secondsRound = Math.floor((my_time / 1000) % 60)
            const millisecondRound = Math.floor(my_time % 1000)
            const time = {
                day: daysRound > 9 ? daysRound : '0' + daysRound, //天
                hour: hoursRound > 9 ? hoursRound : '0' + hoursRound, //小时,
                minutes: minutesRound > 9 ? minutesRound : '0' + minutesRound, //分.
                seconds: secondsRound > 9 ? secondsRound : '0' + secondsRound, //秒。
                millisecond: millisecondRound > 9 ? millisecondRound : '00' + millisecondRound, //毫秒。
                DD:Math.floor(my_time / 1000 / 60 / 60 / 24),
                HH:Math.floor(my_time / 1000 / 60 / 60),
                MM:Math.floor(my_time / 1000 / 60),
                SS:Math.floor(my_time / 1000),
                MS:my_time,
            }
            return time
        },
        start() {
            clearInterval(this.countDownId)
            this.$emit('start')
            this.countDownId = setInterval(() => {
                let lst = this.now + 50
                if (lst > this.time) {
                    clearInterval(this.countDownId)
                    this.isfinish = true
                    this.$emit('end')
                    return
                }
                this.isfinish = false
                this.now = lst
                this.$emit('change', this.time_data)
            }, 50)
        },
        // 停止，直接结束。
        stop() {
            clearInterval(this.countDownId)
            this.now = this.time
            this.$emit('end')
        },
        pause() {
            clearInterval(this.countDownId)
        },
        resinit() {
            clearInterval(this.countDownId)
            this.now = 0
            this.isfinish = true
        }
    }
}

function useFetch(that,url,opt={},loginUrl=null){
    let params = {method:'GET',headers:{}};
    if(opt['method']) params.method = opt['method'];
    if(opt.params){
        url =url + (url.indexOf('?') > -1 ? '&':'?') + new URLSearchParams(opt.params);
    }
    if(params.method !== 'GET') params.headers['Content-Type'] = 'application/json';
    params.headers['Accept'] = '*/*,json';
    if(opt['data']) params.body = JSON.stringify(opt['data']);
    return new Promise((resolve,reject) =>{
        fetch(url,params).then((res) => {
            return res.json()
        }).then((json) => {
            if(json.status !== 0){
                return new Promise((resolve,reject) =>{
                    reject(json)
                } );
            }
            resolve(json)
        }).catch((err) => {
            if(err.status && err.status === 422){
                for (let key in err.errors){
                    that.$notify.warning({
                        title: '校验提示',
                        content: err.errors[key][0],
                        closeBtn:true
                    })
                }
                that.$message.error('请先完成表单在提交')
            }else if(loginUrl!=null && ((err.status && (err.status === 401 || err.status === 402)) || (err.code && (err.code === 402 || err.code === 401)))){
				console.log(111)
				that.$message.error('您还未登入或登入已过期，请重新登入');
				window.location.href = loginUrl;
				return;
			}else{
                that.$message.error(err.message??'异常')
            }
            reject(err)
        })
    })
}