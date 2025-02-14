'use client'

// React Imports
import { useEffect, useState } from 'react'

// Component Imports
import TableData from './TableData'
import { useSettings } from '@core/hooks/useSettings'

import axios from 'axios'

import authConfig from '@configs/auth'

const NewsList = ({ type }: any) => {

    useEffect(() => {
        getNewsDataList()
    }, [])

    const [newsData, setNewsData] = useState<any[]>([])

    const getNewsDataList = async function () {
        try {
            const RS = await axios.post(authConfig.backEndApiHost + 'website/list.php', { type }, {
            headers: {
                'Content-Type': 'application/json'
            }
            }).then(res=>res.data)
            setNewsData(RS)
        }
        catch(Error: any) {
            console.log("getChatLogList Error", Error)
        }
    }

    console.log("newsData", newsData)

    return (
        <>
        </>
    )
}

export default NewsList
