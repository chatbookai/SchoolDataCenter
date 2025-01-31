// ** React Imports
import { useState, useEffect } from 'react'

// ** MUI Imports
import Card from '@mui/material/Card'
import { useTheme } from '@mui/material/styles'
import CardHeader from '@mui/material/CardHeader'
import CardContent from '@mui/material/CardContent'

// ** Third Party Imports
import { ApexOptions } from 'apexcharts'

// ** Component Import
import ReactApexcharts from 'src/@core/components/react-apexcharts'

// ** Custom Components Imports
import OptionsMenu from 'src/@core/components/option-menu'

interface DataType {
  data: {[key:string]:any}
  handleOptionsMenuItemClick: (Item: string) => void
}

const ApexDonutChart = (props: DataType) => {
  
  const { data, handleOptionsMenuItemClick } = props
  const [selectedItem, setSelectedItem] = useState<string>("")

  useEffect(() => {
    data.TopRightOptions.map((item:{[key:string]:any})=>{
      if(item.selected) {
        setSelectedItem(item.name)
      }
    })
  }, [data.TopRightOptions])

  // ** Hook
  const theme = useTheme()

  const options: ApexOptions = {
    stroke: { width: 0 },
    labels: data.dataX,
    colors: data.colors,
    dataLabels: {
      enabled: true,
      formatter: (val: string) => `${parseInt(val, 10)}%`
    },
    legend: {
      position: 'bottom',
      markers: { offsetX: -3 },
      labels: { colors: theme.palette.text.secondary },
      itemMargin: {
        vertical: 3,
        horizontal: 10
      }
    },
    plotOptions: {
      pie: {
        donut: {
          labels: {
            show: true,
            name: {
              fontSize: '1.2rem'
            },
            value: {
              fontSize: '1.2rem',
              color: theme.palette.text.secondary,
              formatter: (val: string) => `${parseInt(val, 10)}`
            },
            total: {
              show: true,
              fontSize: '1.2rem',
              label: data.dataX[0],
              formatter: () => data.dataY[0].data[0],
              color: theme.palette.text.primary
            }
          }
        }
      }
    },
    responsive: [
      {
        breakpoint: 992,
        options: {
          chart: {
            height: 380
          },
          legend: {
            position: 'bottom'
          }
        }
      },
      {
        breakpoint: 576,
        options: {
          chart: {
            height: 320
          },
          plotOptions: {
            pie: {
              donut: {
                labels: {
                  show: true,
                  name: {
                    fontSize: '1rem'
                  },
                  value: {
                    fontSize: '1rem'
                  },
                  total: {
                    fontSize: '1rem'
                  }
                }
              }
            }
          }
        }
      }
    ]
  }

  return (
    <Card>
      <CardHeader
        title={data.Title}
        subheader={data.SubTitle}
        subheaderTypographyProps={{ sx: { color: (theme: any) => `${theme.palette.text.disabled} !important` } }}
        action={
          <OptionsMenu
            options={
              data.TopRightOptions.map((item:{[key:string]:any})=>{
                return {
                  text: item.name,
                  menuItemProps: {
                    sx: { py: 2 },
                    selected: selectedItem === item.name,
                    onClick: () => {
                      handleOptionsMenuItemClick(item.name)
                      console.log(item)
                      setSelectedItem(item.name)
                    }
                  }
                }
              })}
            iconButtonProps={{ size: 'small', sx: { color: 'text.primary' } }}
          />
        }
      />
      <CardContent>
        <ReactApexcharts type='donut' height={333} options={options} series={data.dataY[0].data} />
      </CardContent>
    </Card>
  )
}

export default ApexDonutChart
