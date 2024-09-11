// ** Next Import
import { useEffect, Fragment } from 'react'
import { useRouter } from 'next/router'
import Spinner from 'src/@core/components/spinner'

const TabIndex = () => {
  const router = useRouter()
  const _GET = router.query
  const tab = String(_GET.tab)
  useEffect(() => {
    if(tab && tab != undefined) {
        router.push('/tab/' + tab)
    }
  }, [tab]) 
  
  return (
    <Fragment>
        <Spinner sx={{ height: '100%' }} />
    </Fragment>
  )
}

export default TabIndex
