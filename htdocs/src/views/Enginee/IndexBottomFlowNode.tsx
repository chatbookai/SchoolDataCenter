// ** React Imports
import { Fragment } from 'react'

// ** MUI Imports
import Card from '@mui/material/Card'
import Step from '@mui/material/Step'
import Stepper from '@mui/material/Stepper'
import StepLabel from '@mui/material/StepLabel'
import Typography from '@mui/material/Typography'
import CardContent from '@mui/material/CardContent'
import CardHeader from '@mui/material/CardHeader'

// ** Custom Components Imports
import IndexBottomFlowNodeDot from './IndexBottomFlowNodeDot'

// ** Styled Components
import StepperWrapper from 'src/@core/styles/mui/stepper'

interface IndexBottomFlowNodeType {
    ApprovalNodeFields: string[]
    ApprovalNodeCurrentField: string
    ActiveStep: number
    ApprovalNodeTitle: string
    Memo:string
    DebugSql:string
  }
  
const IndexBottomFlowNode = (props: IndexBottomFlowNodeType) => {
    const { ApprovalNodeFields, ActiveStep, ApprovalNodeTitle, DebugSql, Memo } = props
    
    return (
        <Fragment>
            <Card> 
                { ApprovalNodeFields && ApprovalNodeTitle ? 
                    <Fragment>
                        <CardHeader title={ApprovalNodeTitle} />  
                        <CardContent>
                            <StepperWrapper>
                            { ActiveStep ?
                            <Stepper activeStep={ActiveStep}>
                                {ApprovalNodeFields.map((node, index) => {
                                const labelProps: {
                                    error?: boolean
                                } = {}
                                
                                return (
                                    <Step key={index}>
                                    <StepLabel {...labelProps} StepIconComponent={IndexBottomFlowNodeDot}>
                                        <div className='step-label'>
                                        <Typography className='step-number'>{`0${index + 1}`}</Typography>
                                        <div>
                                            <Typography className='step-title'>{node}</Typography>
                                        </div>
                                        </div>
                                    </StepLabel>
                                    </Step>
                                )
                                })}
                            </Stepper>
                            :
                            null
                            }
                            </StepperWrapper>
                        </CardContent>
                    </Fragment>
                    : '' 
                }
                { DebugSql ?
                        <CardContent>
                            <Typography >{DebugSql}</Typography>
                        </CardContent>
                    : '' 
                }
                { Memo ?
                        <CardContent>
                            <Typography >{Memo}</Typography>
                        </CardContent>
                    : '' 
                }
            </Card>
            
        </Fragment>
    )
}

export default IndexBottomFlowNode
