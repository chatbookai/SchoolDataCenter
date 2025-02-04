// ** MUI Imports
import Card from '@mui/material/Card'
import { useTheme } from '@mui/material/styles'
import CardHeader from '@mui/material/CardHeader'
import CardContent from '@mui/material/CardContent'

import { ApexOptions } from 'apexcharts'

// ** Component Import
import ReactApexcharts from 'src/@core/components/react-apexcharts'

const ApexBarChart = (props: any) => {

  const { dataSource } = props

  const dataJson = JSON.parse(dataSource)
  const data = dataJson.Chart
  console.log("dataJsonData", data)

  // ** Hook
  const theme = useTheme()

  const options: ApexOptions = {
    chart: {
      parentHeightOffset: 0,
      toolbar: { show: true }
    },
    dataLabels: { enabled: true },
    plotOptions: {
      bar: {
        borderRadius: 8,
        barHeight: '30%',
        horizontal: true,
        startingShape: 'rounded'
      }
    },
    grid: {
      borderColor: theme.palette.divider,
      xaxis: {
        lines: { show: true }
      },
      padding: {
        top: -10
      }
    },
    yaxis: {
      labels: {
        style: { colors: theme.palette.text.disabled }
      }
    },
    xaxis: {
      axisBorder: { show: false },
      axisTicks: { color: theme.palette.divider },
      categories: data.dataX,
      labels: {
        style: { colors: theme.palette.text.disabled }
      }
    }
  }

  return (
    <Card>
      <CardHeader
        title={data.Title}
        subheader={data.SubTitle}
        sx={{
          flexDirection: ['column', 'row'],
          alignItems: ['flex-start', 'center'],
          '& .MuiCardHeader-action': { mb: 0 },
          '& .MuiCardHeader-content': { mb: [0] }
        }}
      />
      <CardContent>
        <ReactApexcharts
          type='bar'
          height={400}
          options={options}
          series={data.dataY}
        />
      </CardContent>
    </Card>
  )
}

export default ApexBarChart
