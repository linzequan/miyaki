/**
 * @Author : linzequan958@pingan.com.cn
 * @Date   : 2015-12-29
 * @Time   : 01:54:53
 *
 * @Description: 工具类
 */
var Utils = {

    /**
     * gritter提示
     * @param  {[type]} type [description]
     * @return {[type]}      [description]
     */
    gritter: function(type, text) {
        var class_name = '',
            title = '';
        switch(type) {
            case 'success':
                title = '成功';
                class_name = 'gritter-success';
                break;
            case 'error':
                title = '错误';
                class_name = 'gritter-error';
                break;
            case 'info':
                title = '提示';
                class_name = 'gritter-info';
                break;
        }
        console.log(title, class_name);
        $.gritter.add({
            title: title,
            text: text || '',
            sticky: false,
            time: 3000,
            speed: 500,
            position: 'bottom-right',
            class_name: class_name
        });
    }
};
