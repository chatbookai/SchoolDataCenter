
/*
* 基础架构: 单点低代码开发平台
* 版权所有: 郑州单点科技软件有限公司
* Email: moodle360@qq.com
* Copyright (c) 2023
* License: GPL V3 or Commercial license
*/
import UserList from "src/views/Enginee/index"

const AppChat = () => {
    // ** States
    const backEndApi = "apps/apps_129.php"
    
    return (
        <UserList backEndApi={backEndApi} externalId=''/>
    )
}

export default AppChat
