
/*
* 基础架构: 单点低代码开发平台
* 版权所有: 郑州单点科技软件有限公司
* Email: moodle360@qq.com
* Copyright (c) 2007-2025
* License: GPL V3 or Commercial license
*/
// ** React Imports
import { ReactNode } from 'react'

// ** Layout Import
import BlankLayout from 'src/@core/layouts/BlankLayout'

import UserList from "src/views/Enginee/index"
import { authConfig } from 'src/configs/auth'

const AppChat = () => {
    // ** States
    const backEndApi = "apps/apps_342.php"

  return (
    <UserList authConfig={authConfig} backEndApi={backEndApi} externalId=''/>
    )
}

AppChat.getLayout = (page: ReactNode) => <BlankLayout>{page}</BlankLayout>

export default AppChat
