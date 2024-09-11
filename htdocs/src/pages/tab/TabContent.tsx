
// ** MUI Imports
import Grid from '@mui/material/Grid'
import Card from '@mui/material/Card'
import CardContent from '@mui/material/CardContent'

// ** Enginee File
import UserList from 'src/views/Enginee/index'

interface TabContentType {
    backEndApi: string
    action: string
    id: number
}

const TabContent = (props: TabContentType) => {
    const { backEndApi, action, id } = props
    console.log("TabContent backEndApi action id", backEndApi, action, id)
    
    return (
        <Grid container spacing={6}>
            <Grid item xs={12}>
                <Card>
                    <CardContent sx={{ pr: { xs: 0, sm: 0 }, pb: { xs: -5, sm: -5 }, pt: { xs: 5, sm: 5 }, pl: { xs: 5, sm: 5 } }}>
                        <Grid container spacing={5}>
                            <UserList backEndApi={backEndApi} externalId=''/>
                        </Grid>
                    </CardContent>
                </Card>
            </Grid>
        </Grid>
    )
}

export default TabContent
