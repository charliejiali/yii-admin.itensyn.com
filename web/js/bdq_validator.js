/**
 * Created by ML-Ewen on 2015/6/4.
 */
/* 名称命名规则说明
 * 首字母大写,非常少用的尽可能用Custom
 * 注:如果添加了方法形式的,记得在Validate中的case中补上
 * --------------------------------------------------------
 * zxh:增加了默认的msg提示,以大量减少页面上的msg属性
 * 详细使用，请参考附件的帮助文档!!
 *
 * 参考 http://www.cnblogs.com/steden/archive/2009/10/14/1583022.html
 * 语法：dataType="Require | Chinese | English | Number | Integer | Double | Email | Url | Phone | Mobile | Currency | Zip | IdCard | QQ | Date | SafeString | Repeat | Compare | Range | Limit | LimitB | Group | Custom | Filter "
 *
 Require	必填项
 Chinese	中文
 English	英文

 Number 数字
 Integer 整数
 Double 实数
 Email Email地址格式
 Url 基于HTTP协议的网址格式
 Phone 电话号码格式
 Mobile 手机号码格式
 Currency 货币格式
 Zip 邮政编码
 IdCard 身份证号码
 QQ QQ号码
 Date 日期
 SafeString 安全密码
 Repeat 重复输入
 Compare 关系比较
 Range 输入范围
 Limit 限制输入长度
 LimitB 限制输入的字节长度
 Group	验证单/多选按钮组
 Custom	自定义正则表达式验证
 *
 */
var __BDP_VDT = {
    Account: {"value": /^[A-Za-z]{1}([A-Za-z0-9\-_]+)?$/, "msg": "只能以英文字母开头,允许英文字母、数字、中下划线"},
    Chinese: {"value": /^[\u0391-\uFFE5]+$/, "msg": "只允许中文"},
    Currency: {"value": /^\d+(\.\d+)?$/, "msg": ""},
    Email: {"value": /^\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*$/, "msg": "请输入正确的E-mail,如test@test.com"},
    English: {"value": /^[A-Za-z]+$/, "msg": "只允许英文字母"},
    IdCard: {"value": /^\d{15}(\d{2}[A-Za-z0-9\*])?$/, "msg": "只能输入18位的身份证号码"},
    Mobile: {"value": /^1[3|4|5|7|8]\d{9}$/, "msg": "手机格式不对"},
    MobilePhone: {"value": /^((0\d{2,3}(\d{6,15}))|(1\d{10}))$/, "msg": "直接输入手机号码或带区号的其他电话号码(只限数字)"},
    Number: {"value": /^\d+$/, "msg": "请输入数值"},
    UnSafe: {"value": /^(([A-Z]*|[a-z]*|\d*|[-_\~!@#\$%\^&\*\.\(\)\[\]\{\}<>\?\\\/\'\"]*)|.{0,5})$|\s/, "msg": ""},
    Phone: {
        "value": /^((\(\d{3}\))|(\d{3}\-))?(\(0\d{2,3}\)|0\d{2,3}-)?[1-9]\d{6,7}([\-0-9]+)?[^\D]{1}$/,
        "msg": "请输入正确电话号码"
    },
    QQ: {"value": /^[1-9]\d{4,9}$/, "msg": "请输入5-10位数的纯数字"},
    UnQueryString: {"value": /[-_\~!@#\$%\^&\*\.\(\)\[\]\{\}<>\?\\\/\'\"]+/, "msg": "不安全字符串"},
    Require: {"value": /.+/, "msg": "不能为空"},
    VRequire: {"value": /\S+/, "msg": "不能为空,必须输入任意非空字符"},
    Url: {"value": /^http:\/\/[A-Za-z0-9]+\.[A-Za-z0-9]+[\/=\?%\-&_~`@[\]\':+!]*([^<>\"\"])*$/, "msg": "请输入正确的URL地址"},
    Trim: {"value": /^[^\s]{1}(.+)?[^\s]{1}$/, "msg": "不能为空,并且前后不能有空格"},
    Zip: {"value": /^[1-9]\d{5}$/, "msg": "邮政编码不存在"},
    NumLength: {"value": /^[1-9]\d{3}$/, "msg": "长度小于5"},
    Double: {"value": "this.IsNumber(value, true)", "msg": "请输入数字"},
    PlusDouble: {"value": "this.IsPlusNumber(value)", "msg": "请输入正数"},
    MinusDouble: {"value": "this.IsMinusNumber(value)", "msg": "请输入负数"},
    Integer: {"value": "this.IsInteger(value)", "msg": "请输入整数"},
    PlusInteger: {"value": "this.IsPlusInteger(value)", "msg": "请输入正整数"},
    MinusInteger: {"value": "this.IsMinusInteger(value)", "msg": "请输入负整数"},

    Compare: {"value": "this.compare(value,getAttribute('operator'),getAttribute('to'))", "msg": ""},
    Custom: {"value": "this.Exec(value, getAttribute('regexp'))", "msg": ""},
    Date: {"value": "this.IsDate(value, getAttribute('min'), getAttribute('format'))", "msg": "格式不正确"},
    Filename: {"value": "this.IsFilename(value)", "msg": "文件名不能为空,且不能包含下列字符 \\ \/ \: \* \? \" < >"},
    Filter: {"value": "this.DoFilter(value, getAttribute('accept'))", "msg": ""},
    Group: {"value": "this.MustChecked(getAttribute('name'), getAttribute('min'), getAttribute('max'))", "msg": ""},
    Limit: {"value": "this.Limit(value.length,getAttribute('min'), getAttribute('max'))", "msg": ""},
    LimitB: {"value": "this.Limit(this.LenB(value), getAttribute('min'), getAttribute('max'))", "msg": ""},
    Repeat: {"value": "value == document.getElementById(getAttribute('to')).value", "msg": "重复输入不一致"},
    Range: {"value": "getAttribute('min') < (value|0) && (value|0) < getAttribute('max')", "msg": ""},
    SafeQueryString: {"value": "this.IsSafeQuery(value)", "msg": "含有不安全字符串,如\"!@#$%^&*'等等"},
    SafeString: {"value": "this.IsSafe(value)", "msg": "密码不符合安全规则"},

    ErrorItem: [document.forms[0]],
    ErrorMessage: ["\u4ee5\u4e0b\u539f\u56e0\u5bfc\u81f4\u63d0\u4ea4\u5931\u8d25\uff1a\t\t\t\t"],//ErrorMessage:["以下原因导致提交失败：\t\t\t\t"],

    Validate: function (formID, mode) {
        var theForm = document.getElementById(formID);

        var theEvent = window.event || arguments.callee.caller.arguments[0];
        var srcElement = theEvent.srcElement;
        if (!srcElement){
            srcElement = theEvent.target;
        }

        var obj = theForm || srcElement;
        var count = obj.elements.length;
        this.ErrorMessage.length = 1;
        this.ErrorItem.length = 1;
        this.ErrorItem[0] = obj;
        for (var i = 0; i < count; i++) {
            with (obj.elements[i]) {
                var _dataType = getAttribute("dataType");
                if (typeof (_dataType) == "object" || typeof (this[_dataType]) == "undefined") {
                    continue;
                }
                this.ClearState(obj.elements[i]);
                if (getAttribute("require") == "false" && value == "") {
                    continue;
                }
                switch (_dataType) {
                    case "Double":
                    case "PlusDouble":
                    case "MinusDouble":
                    case "Integer":
                    case "PlusInteger":
                    case "MinusInteger":

                    case "Compare":
                    case "Custom":
                    case "Date":
                    case "Filename":
                    case "Filter":
                    case "Group":
                    case "Limit":
                    case "LimitB":
                    case "Repeat":
                    case "Range":
                    case "SafeQueryString":
                    case "SafeString":
                        if (!eval(this[_dataType].value)) {
                            if (getAttribute("msg") == null) {
                                this.AddError(i, this[_dataType].msg);
                            }
                            else {
                                this.AddError(i, getAttribute("msg"));
                            }
                        }
                        break;
                    default:
                        if (!this[_dataType].value.test(value)) {
                            if (getAttribute("msg") == null) {
                                this.AddError(i, this[_dataType].msg);
                            }
                            else {
                                this.AddError(i, getAttribute("msg"));
                            }
                        }
                        break;
                }
            }
        }
        if (this.ErrorMessage.length > 1) {
            mode = mode || 3;
            var errCount = this.ErrorItem.length;
            switch (mode) {
                case 1://弹出提示
                    alert(this.ErrorMessage.join("\n"));
                    //this.ErrorItem[1].focus();
                    break;
                case 2://变红并显示错误信息
                    for (var i = 1; i < errCount; i++) {
                        this.ErrorItem[i].style.color = "#ff0000";
                    }
                case 3://显示错误信息
                    for (var i = 1; i < errCount; i++) {
                        try {
                            var span = document.createElement("span");
                            span.className = "input-error";
                            span.innerHTML = this.ErrorMessage[i].replace(/\d+:/, "*");
                            this.ErrorItem[i].parentNode.appendChild(span);
                            //this.ErrorItem[i].className = "input-warn";
                        }
                        catch (e) {
                            alert(e.description);
                        }
                    }
                    //this.ErrorItem[1].focus();
                    break;
                default:
                    alert(this.ErrorMessage.join("\n"));
                    break;
            }
            return false;
        }
        return true;
    },

    ValidateSingle: function (elem, mode) {
        var ErrorMsg = [];
        with (elem) {
            var _dataType = getAttribute("dataType");
            if (typeof (_dataType) == "object" || typeof (this[_dataType]) == "undefined") {
                return true;
            }
            this.ClearState(elem);
            if (getAttribute("require") == "false" && value == "") {
                return true;
            }
            switch (_dataType) {
                case "Double":
                case "PlusDouble":
                case "MinusDouble":
                case "Integer":
                case "PlusInteger":
                case "MinusInteger":

                case "Compare":
                case "Custom":
                case "Date":
                case "Filename":
                case "Filter":
                case "Group":
                case "Limit":
                case "LimitB":
                case "Repeat":
                case "Range":
                case "SafeQueryString":
                case "SafeString":
                    if (!eval(this[_dataType].value)) {
                        if (getAttribute("msg") == null) {
                            ErrorMsg.push(this[_dataType].msg);
                        }
                        else {
                            ErrorMsg.push(getAttribute("msg"));
                        }
                    }
                    break;
                default:
                    if (!this[_dataType].value.test(value)) {
                        if (getAttribute("msg") == null) {
                            ErrorMsg.push(this[_dataType].msg);
                            //this.AddError(i, this[_dataType].msg);
                        }
                        else {
                            ErrorMsg.push(getAttribute("msg"));
                            //this.AddError(i, getAttribute("msg"));
                        }
                    }
                    break;
            }
        }
        if (ErrorMsg.length > 0) {
            mode = mode || 0;
            var errCount = this.ErrorItem.length;
            switch (mode) {
                case 1://弹出提示
                    alert(ErrorMsg.join("\n"));
                    break;
                case 2://变红并弹出提示
                    elem.style.color = "#bf0000";
                case 3://显示错误信息
                    try {
                        var span = document.createElement("span");
                        span.className = "input-error";
                        span.innerHTML = ErrorMsg[0].replace(/\d+:/, "*");
                        elem.parentNode.appendChild(span);
                    }
                    catch (e) {
                        alert(e.description);
                    }
                    break;
                default:
                    break;
            }
            return false;
        }
        return true;
    },

    IsSafeQuery: function (str) {
        return !this.UnQueryString.value.test(str);
    },
    IsSafe: function (str) {
        return !this.UnSafe.value.test(str);
    },
    Limit: function (len, min, max) {
        min = min || 0;
        max = max || Number.MAX_VALUE;
        return min <= len && len <= max;
    },
    LenB: function (str) {
        return str.replace(/[^\x00-\xff]/g, "**").length;
    },
    ClearState: function (elem) {
        with (elem) {
            var lastNode = parentNode.childNodes[parentNode.childNodes.length - 1];
            if (lastNode.className == "input-error") {
                parentNode.removeChild(lastNode);
            }
        }
    },
    AddError: function (index, str) {
        this.ErrorItem[this.ErrorItem.length] = this.ErrorItem[0].elements[index];
        this.ErrorMessage[this.ErrorMessage.length] = this.ErrorMessage.length + ":" + str;
    },
    Exec: function (op, reg) {
        return new RegExp(reg, "g").test(op);
    },
    compare: function (op1, operator, op2) {
        switch (operator) {
            case "NotEqual":
                return (op1 != op2);
            case "GreaterThan":
                return (op1 > op2);
            case "GreaterThanEqual":
                return (op1 >= op2);
            case "LessThan":
                return (op1 < op2);
            case "LessThanEqual":
                return (op1 <= op2);
            default:
                return (op1 == op2);
        }
    },

    MustChecked: function (name, min, max) {
        var groups = document.getElementsByName(name);
        var hasChecked = 0;
        min = min || 1;
        max = max || groups.length;
        for (var i = groups.length - 1; i >= 0; i--) {
            if (groups[i].checked) {
                hasChecked++;
            }
        }
        return min <= hasChecked && hasChecked <= max;
    },
    DoFilter: function (input, filter) {
        return new RegExp("^.+\.(?=EXT)(EXT)$".replace(/EXT/g, filter.split(/\s*,\s*/).join("|")), "gi").test(input);
    },
    IsDate: function (op, formatString) {
        formatString = formatString || "ymd";
        var m, year, month, day;
        switch (formatString) {
            case "ymd":
                m = op.match(new RegExp("^((\\d{4})|(\\d{2}))([-./])(\\d{1,2})\\4(\\d{1,2})$"));
                if (m == null) {
                    return false;
                }
                day = m[6];
                month = m[5] * 1;
                year = (m[2].length == 4) ? m[2] : GetFullYear(parseInt(m[3], 10));
                break;
            case "dmy":
                m = op.match(new RegExp("^(\\d{1,2})([-./])(\\d{1,2})\\2((\\d{4})|(\\d{2}))$"));
                if (m == null) {
                    return false;
                }
                day = m[1];
                month = m[3] * 1;
                year = (m[5].length == 4) ? m[5] : GetFullYear(parseInt(m[6], 10));
                break;
            default:
                break;
        }
        if (!parseInt(month)) {
            return false;
        }
        month = month == 0 ? 12 : month;
        var date = new Date(year, month - 1, day);
        return (typeof(date) == "object" && year == date.getFullYear() && month == (date.getMonth() + 1) && day == date.getDate());
        function GetFullYear(y) {
            return ((y < 30 ? "20" : "19") + y) | 0;
        }
    },
    //合法文件名,文件名不能包含\/:*?"<>
    IsFilename: function (v) {
        if (v.length == 0) {
            return false;
        }
        if (v.indexOf("\\") == -1
            && v.indexOf("\/") == -1
            && v.indexOf("\:") == -1
            && v.indexOf("\*") == -1
            && v.indexOf("\?") == -1
            && v.indexOf("\"") == -1
            && v.indexOf("<") == -1
            && v.indexOf(">") == -1
        ) {
            return true;
        }
        return false;
    },
    //isPlusSign正数是否可带+号,true可带,false不可带
    //判断是否为格式正确的数字,小数点后可带0(如可以为-1,1,1.1等等)
    IsNumber: function (v, isPlusSign) {
        if (!isNaN(v)) {
            if (v.length == 0 || (!isPlusSign && v.indexOf("+") != -1)) {
                return false;
            }
            if (v.indexOf(".") == 0
                || v.indexOf("-.") == 0
                || v.indexOf("00") == 0
                || v.indexOf("-00") == 0
                || v.lastIndexOf(".") == v.length - 1
            ) {
                return false;
            }
            return true;
        }
        return false;
    },
    //判断是否为正值数字(如可以为0,1.1等等)
    IsPlusNumber: function (v) {
        if (this.IsNumber(v, true)) {
            if (v.indexOf("-") != -1) {
                return false;
            }
            return true;
        }
        return false;
    },
    //判断是否为负值数字(如可以为-1.1,-2等等)
    IsMinusNumber: function (v) {
        if (this.IsNumber(v, false)) {
            if (v.indexOf("-") != -1) {
                return true;
            }
        }
        return false;
    },
    //判断是否为整数(如可以为-1,1等等)
    IsInteger: function (v) {
        if (this.IsNumber(v, true)) {
            if (v.indexOf(".") != -1) {
                return false;
            }
            return true;
        }
        return false;
    },
    //判断是否为正整数(如可以为2等等)
    IsPlusInteger: function (v) {
        if (this.IsInteger(v)) {
            if (v.indexOf("-") != -1) {
                return false;
            }
            return true;
        }
        return false;
    },
    //判断是否为负整数(如可以为-2,-0等等,注0只能为-0)
    IsMinusInteger: function (v) {
        if (this.IsInteger(v)) {
            if (v.indexOf("-") != -1) {
                return true;
            }
        }
        return false;
    },

    //默认自带一些校验方法
    //添加校验
    add_submit: function (submitid) {
        if (this.Validate("addForm", 3)) {
            if (!confirm("是否确定提交?")) {
                return false;
            }
            document.getElementById(submitid).click();
            return true;
        }
        return false;
    },
    //更新校验
    upd_submit: function (submitid) {
        if (this.Validate("updForm", 3)) {
            if (!confirm("是否确定保存?")) {
                return false;
            }
            document.getElementById(submitid).click();
            return true;
        }
        return false;
    },
    //查询校验
    query_submit: function (submitid) {
        if (this.Validate("queryForm", 3)) {
            document.getElementById(submitid).click();
            return true;
        }
        return false;
    },
    //其它
    form_submit: function (formid, submitid) {
        if (this.Validate(formid, 3)) {
            if (!confirm("是否确定提交?")) {
                return false;
            }
            document.getElementById(submitid).click();
            return true;
        }
        return false;
    }
};


/*
 用途 ： 校验ip地址的格式
 输入 ： strIP ： ip地址
 返回 ： 如果通过验证返回true, 否则返回false ；
 */
function isIP(strIP) {
    if (isNull(strIP)) return false;
    var re = /^(\d+)\.(\d+)\.(\d+)\.(\d+)$/g //匹配IP地址的正则表达式
    if (re.test(strIP)) {
        if (RegExp.$1 < 256 && RegExp.$2 < 256 && RegExp.$3 < 256 && RegExp.$4 < 256) return true;
    }
    return false;
}

/*
 用途：检查输入字符串是否为空或者全部都是空格
 输入：str
 返回：
 如果全是空返回true,否则返回false
 */
function isNull(str) {
    if (str == "") return true;
    var regu = "^[ ]+$";
    var re = new RegExp(regu);
    return re.test(str);
}


/*
 用途：检查输入对象的值是否符合整数格式
 输入：str 输入的字符串
 返回：如果通过验证返回true,否则返回false

 */
function isInteger(str) {
    var regu = /^[-]{0,1}[0-9]{1,}$/;
    return regu.test(str);
}

/*
 用途：检查输入手机号码是否正确
 输入：
 s：字符串
 返回：
 如果通过验证返回true,否则返回false
 */
function checkMobile(s) {
    var regu = /^[1][3][0-9]{9}$/;
    var re = new RegExp(regu);
    if (re.test(s)) {
        return true;
    } else {
        return false;
    }
}


/*
 用途：检查输入字符串是否符合正整数格式
 输入：
 s：字符串
 返回：
 如果通过验证返回true,否则返回false

 */
function isNumber(s) {
    var regu = "^[0-9]+$";
    var re = new RegExp(regu);
    if (s.search(re) != -1) {
        return true;
    } else {
        return false;
    }
}

/*
 用途：检查输入字符串是否是带小数的数字格式,可以是负数
 输入：
 s：字符串
 返回：
 如果通过验证返回true,否则返回false

 */
function isDecimal(str) {
    if (isInteger(str)) return true;
    var re = /^[-]{0,1}(\d+)[\.]+(\d+)$/;
    if (re.test(str)) {
        if (RegExp.$1 == 0 && RegExp.$2 == 0) return false;
        return true;
    } else {
        return false;
    }
}

/*
 用途：检查输入对象的值是否符合端口号格式
 输入：str 输入的字符串
 返回：如果通过验证返回true,否则返回false

 */
function isPort(str) {
    return (isNumber(str) && str < 65536);
}

/*
 用途：检查输入对象的值是否符合E-Mail格式
 输入：str 输入的字符串
 返回：如果通过验证返回true,否则返回false

 */
function isEmail(str) {
    var myReg = /^[-_A-Za-z0-9]+@([_A-Za-z0-9]+\.)+[A-Za-z0-9]{2,3}$/;
    if (myReg.test(str)) return true;
    return false;
}

/*
 用途：检查输入字符串是否符合金额格式
 格式定义为带小数的正数，小数点后最多三位
 输入：
 s：字符串
 返回：
 如果通过验证返回true,否则返回false

 */
function isMoney(s) {
    var regu = "^[0-9]+[\.][0-9]{0,3}$";
    var re = new RegExp(regu);
    if (re.test(s)) {
        return true;
    } else {
        return false;
    }
}
/*
 用途：检查输入字符串是否只由英文字母和数字和下划线组成
 输入：
 s：字符串
 返回：
 如果通过验证返回true,否则返回false

 */
function isNumberOr_Letter(s) {//判断是否是数字或字母

    var regu = "^[0-9a-zA-Z\_]+$";
    var re = new RegExp(regu);
    if (re.test(s)) {
        return true;
    } else {
        return false;
    }
}
/*
 用途：检查输入字符串是否只由英文字母和数字组成
 输入：
 s：字符串
 返回：
 如果通过验证返回true,否则返回false

 */
function isNumberOrLetter(s) {//判断是否是数字或字母

    var regu = "^[0-9a-zA-Z]+$";
    var re = new RegExp(regu);
    if (re.test(s)) {
        return true;
    } else {
        return false;
    }
}
/*
 用途：检查输入字符串是否只由汉字、字母、数字组成
 输入：
 value：字符串
 返回：
 如果通过验证返回true,否则返回false

 */
function isChinaOrNumbOrLett(s) {//判断是否是汉字、字母、数字组成

    var regu = "^[0-9a-zA-Z\u4e00-\u9fa5]+$";
    var re = new RegExp(regu);
    if (re.test(s)) {
        return true;
    } else {
        return false;
    }
}

/*
 用途：判断是否是日期
 输入：date：日期；fmt：日期格式
 返回：如果通过验证返回true,否则返回false
 */
function isDate(date, fmt) {
    if (fmt == null) fmt = "yyyyMMdd";
    var yIndex = fmt.indexOf("yyyy");
    if (yIndex == -1) return false;
    var year = date.substring(yIndex, yIndex + 4);
    var mIndex = fmt.indexOf("MM");
    if (mIndex == -1) return false;
    var month = date.substring(mIndex, mIndex + 2);
    var dIndex = fmt.indexOf("dd");
    if (dIndex == -1) return false;
    var day = date.substring(dIndex, dIndex + 2);
    if (!isNumber(year) || year > "2100" || year < "1900") return false;
    if (!isNumber(month) || month > "12" || month < "01") return false;
    if (day > getMaxDay(year, month) || day < "01") return false;
    return true;
}

function getMaxDay(year, month) {
    if (month == 4 || month == 6 || month == 9 || month == 11)
        return "30";
    if (month == 2)
        if (year % 4 == 0 && year % 100 != 0 || year % 400 == 0)
            return "29";
        else
            return "28";
    return "31";
}

/*
 用途：字符1是否以字符串2结束
 输入：str1：字符串；str2：被包含的字符串
 返回：如果通过验证返回true,否则返回false

 */
function isLastMatch(str1, str2) {
    var index = str1.lastIndexOf(str2);
    if (str1.length == index + str2.length) return true;
    return false;
}


/*
 用途：字符1是否以字符串2开始
 输入：str1：字符串；str2：被包含的字符串
 返回：如果通过验证返回true,否则返回false

 */
function isFirstMatch(str1, str2) {
    var index = str1.indexOf(str2);
    if (index == 0) return true;
    return false;
}

/*
 用途：字符1是包含字符串2
 输入：str1：字符串；str2：被包含的字符串
 返回：如果通过验证返回true,否则返回false

 */
function isMatch(str1, str2) {
    var index = str1.indexOf(str2);
    if (index == -1) return false;
    return true;
}


/*
 用途：检查输入的起止日期是否正确，规则为两个日期的格式正确，
 且结束如期>=起始日期
 输入：
 startDate：起始日期，字符串
 endDate：结束如期，字符串
 返回：
 如果通过验证返回true,否则返回false

 */
function checkTwoDate(startDate, endDate) {
    if (!isDate(startDate)) {
        alert("起始日期不正确!");
        return false;
    } else if (!isDate(endDate)) {
        alert("终止日期不正确!");
        return false;
    } else if (startDate > endDate) {
        alert("起始日期不能大于终止日期!");
        return false;
    }
    return true;
}

/*
 用途：检查输入的Email信箱格式是否正确
 输入：
 strEmail：字符串
 返回：
 如果通过验证返回true,否则返回false

 */
function checkEmail(strEmail) {
//var emailReg = /^[_a-z0-9]+@([_a-z0-9]+\.)+[a-z0-9]{2,3}$/;
    var emailReg = /^[\w-]+(\.[\w-]+)*@[\w-]+(\.[\w-]+)+$/;
    if (emailReg.test(strEmail)) {
        return true;
    } else {
        alert("您输入的Email地址格式不正确！");
        return false;
    }
}

/*
 用途：检查输入的电话号码格式是否正确
 输入：
 strPhone：字符串
 返回：
 如果通过验证返回true,否则返回false

 */
function checkPhone(strPhone) {
    var phoneRegWithArea = /^[0][1-9]{2,3}-[0-9]{5,10}$/;
    var phoneRegNoArea = /^[1-9]{1}[0-9]{5,8}$/;
    var prompt = "您输入的电话号码不正确!"
    if (strPhone.length > 9) {
        if (phoneRegWithArea.test(strPhone)) {
            return true;
        } else {
            alert(prompt);
            return false;
        }
    } else {
        if (phoneRegNoArea.test(strPhone)) {
            return true;
        } else {
            alert(prompt);
            return false;
        }


    }
}


/*
 用途：检查复选框被选中的数目
 输入：
 checkboxID：字符串
 返回：
 返回该复选框中被选中的数目

 */

function checkSelect(checkboxID) {
    var check = 0;
    var i = 0;
    if (document.all(checkboxID).length > 0) {
        for (i = 0; i < document.all(checkboxID).length; i++) {
            if (document.all(checkboxID).item(i).checked) {
                check += 1;
            }


        }
    } else {
        if (document.all(checkboxID).checked)
            check = 1;
    }
    return check;
}

function getTotalBytes(varField) {
    if (varField == null)
        return -1;

    var totalCount = 0;
    for (i = 0; i < varField.value.length; i++) {
        if (varField.value.charCodeAt(i) > 127)
            totalCount += 2;
        else
            totalCount++;
    }
    return totalCount;
}

function getFirstSelectedValue(checkboxID) {
    var value = null;
    var i = 0;
    if (document.all(checkboxID).length > 0) {
        for (i = 0; i < document.all(checkboxID).length; i++) {
            if (document.all(checkboxID).item(i).checked) {
                value = document.all(checkboxID).item(i).value;
                break;
            }
        }
    } else {
        if (document.all(checkboxID).checked)
            value = document.all(checkboxID).value;
    }
    return value;
}


function getFirstSelectedIndex(checkboxID) {
    var value = -2;
    var i = 0;
    if (document.all(checkboxID).length > 0) {
        for (i = 0; i < document.all(checkboxID).length; i++) {
            if (document.all(checkboxID).item(i).checked) {
                value = i;
                break;
            }
        }
    } else {
        if (document.all(checkboxID).checked)
            value = -1;
    }
    return value;
}

function selectAll(checkboxID, status) {

    if (document.all(checkboxID) == null)
        return;

    if (document.all(checkboxID).length > 0) {
        for (i = 0; i < document.all(checkboxID).length; i++) {

            document.all(checkboxID).item(i).checked = status;
        }
    } else {
        document.all(checkboxID).checked = status;
    }
}

function selectInverse(checkboxID) {
    if (document.all(checkboxID) == null)
        return;

    if (document.all(checkboxID).length > 0) {
        for (i = 0; i < document.all(checkboxID).length; i++) {
            document.all(checkboxID).item(i).checked = !document.all(checkboxID).item(i).checked;
        }
    } else {
        document.all(checkboxID).checked = !document.all(checkboxID).checked;
    }
}

function checkDate(value) {
    if (value == '') return true;
    if (value.length != 8 || !isNumber(value)) return false;
    var year = value.substring(0, 4);
    if (year > "2100" || year < "1900")
        return false;

    var month = value.substring(4, 6);
    if (month > "12" || month < "01") return false;

    var day = value.substring(6, 8);
    if (day > getMaxDay(year, month) || day < "01") return false;

    return true;
}

/*
 用途：检查输入的起止日期是否正确，规则为两个日期的格式正确或都为空
 且结束日期>=起始日期
 输入：
 startDate：起始日期，字符串
 endDate：  结束日期，字符串
 返回：
 如果通过验证返回true,否则返回false

 */
function checkPeriod(startDate, endDate) {
    if (!checkDate(startDate)) {
        alert("起始日期不正确!");
        return false;
    } else if (!checkDate(endDate)) {
        alert("终止日期不正确!");
        return false;
    } else if (startDate > endDate) {
        alert("起始日期不能大于终止日期!");
        return false;
    }
    return true;
}

/*
 用途：检查证券代码是否正确
 输入：
 secCode:证券代码
 返回：
 如果通过验证返回true,否则返回false

 */
function checkSecCode(secCode) {
    if (secCode.length != 6) {
        alert("证券代码长度应该为6位");
        return false;
    }

    if (!isNumber(secCode)) {
        alert("证券代码只能包含数字");


        return false;
    }
    return true;
}

/****************************************************
 function:cTrim(sInputString,iType)
 description:字符串去空格的函数
 parameters:iType：1=去掉字符串左边的空格

 2=去掉字符串左边的空格
 0=去掉字符串左边和右边的空格
 return value:去掉空格的字符串
 ****************************************************/
function cTrim(sInputString, iType) {
    var sTmpStr = ' ';
    var i = -1;

    if (iType == 0 || iType == 1) {
        while (sTmpStr == ' ') {
            ++i;
            sTmpStr = sInputString.substr(i, 1);
        }
        sInputString = sInputString.substring(i);
    }

    if (iType == 0 || iType == 2) {
        sTmpStr = ' ';
        i = sInputString.length;
        while (sTmpStr == ' ') {
            --i;
            sTmpStr = sInputString.substr(i, 1);
        }
        sInputString = sInputString.substring(0, i + 1);
    }
    return sInputString;
}

