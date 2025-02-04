import React, { useState, useRef, useEffect } from 'react';
import { Box, Grid, Button, ButtonGroup, TextField, Typography, MenuItem } from '@mui/material';
import {
  CheckCircle as SingleIcon,
  Checklist as MultipleIcon,
  Check as JudgeIcon,
  Create as FillIcon,
  QuestionAnswer as QAIcon,
  AddCircle as GenerateIcon
} from '@mui/icons-material';
import { authConfig, defaultConfig } from 'src/configs/auth'
import { filterDeepSeekAiResultToText } from 'src/functions/ChatBook'
import { marked } from 'marked'

import AddOrEditTable from './AddOrEditTable'
import toast from 'react-hot-toast'

marked.setOptions({
  renderer: new marked.Renderer(),
  gfm: true,
  async: false,
  breaks: true,
  pedantic: false,
  silent: true
})

const AppAiQuestion = (props: any) => {

  const { setPageModel, selectItem, store, externalId, addEditActionId, toggleEditTableDrawer, addUserHandleFilter, backEndApi, editViewCounter, isGetStructureFromEditDefault, CSRF_TOKEN, toggleImagesPreviewListDrawer, handleIsLoadingTipChange, setForceUpdate } = props

  const stopMsg = false;
  const [processingMessage, setProcessingMessage] = useState("")
  const [processingQuestionResult, setProcessingQuestionResult] = useState<boolean>(false)
  const [generating, setGenerating] = useState<boolean>(false);
  const [tabIndex, setTabIndex] = useState(0);
  const [requirements, setRequirements] = useState('');
  const [difficulties, setDifficulties] = useState<any>({
    single: 'easy',
    multiple: 'easy',
    judge: 'easy',
    fill: 'easy',
    qa: 'easy'
  });
  const [questionCounts, setQuestionCounts] = useState<any>({
    single: 2,
    multiple: 2,
    judge: 2,
    fill: 2,
    qa: 2
  });

  // 课程列表
  const courses = ['语文', '数学', '英语', '物理', '化学', '生物', '历史', '地理', '政治'];
  const [course, setCourse] = useState(courses[0]);

  // 年级与出版社映射
  const gradeTextbookMap: Record<string, string[]> = {
    '小学一年级': ['人教版', '北师大版'],
    '小学二年级': ['人教版', '北师大版', '苏教版'],
    '小学三年级': ['人教版', '北师大版', '苏教版'],
    '小学四年级': ['人教版', '北师大版', '苏教版', '沪教版'],
    '小学五年级': ['人教版', '北师大版', '苏教版', '沪教版'],
    '小学六年级': ['人教版', '北师大版', '苏教版', '沪教版'],
    '初中一年级': ['人教版', '北师大版', '苏教版', '沪教版', '浙教版'],
    '初中二年级': ['人教版', '北师大版', '苏教版', '沪教版', '浙教版'],
    '初中三年级': ['人教版', '北师大版', '苏教版', '沪教版', '浙教版'],
    '高中一年级': ['人教版', '北师大版', '苏教版', '沪教版', '浙教版', '湘教版'],
    '高中二年级': ['人教版', '北师大版', '苏教版', '沪教版', '浙教版', '湘教版'],
    '高中三年级': ['人教版', '北师大版', '苏教版', '沪教版', '浙教版', '湘教版']
  };

  const [grade, setGrade] = useState('小学一年级');
  const [textbook, setTextbook] = useState(gradeTextbookMap['小学一年级'][0]);

  const additionalParameters = {教师姓名: selectItem.教师姓名, 班级名称: selectItem.班级名称, 课程名称: selectItem.课程名称, 学期名称: selectItem.学期名称}
  console.log("additionalParameters", additionalParameters)

  // 题型映射
  const questionTypeMap: any = {
    single: '单选题',
    multiple: '多选题',
    judge: '判断题',
    fill: '填空题',
    qa: '问答题'
  };

  // 难度映射
  const difficultyMap: any = {
    easy: '简单',
    medium: '中等',
    hard: '困难'
  };

  // 题型图标映射
  const questionTypeIcons = {
    single: <SingleIcon />,
    multiple: <MultipleIcon />,
    judge: <JudgeIcon />,
    fill: <FillIcon />,
    qa: <QAIcon />
  };

  const handleGenerate = async () => {
    setGenerating(true)
    setProcessingMessage('正在为您生成测验题目中...')
    setProcessingQuestionResult(false)
    const startTime = performance.now()
    console.log('生成题目参数：', {
      course,
      requirements,
      questionCounts,
      difficulties,
      grade,
      textbook
    });

    authConfig.backEndApiAiBaseUrl = "http://localhost/api/"
    try {
      const authorization = window.localStorage.getItem(defaultConfig.storageTokenKeyName)!
      const response = await fetch(authConfig.backEndApiAiBaseUrl + `aichat/questionai.php`, {
          method: 'POST',
          headers: {
              Authorization: authorization,
              'Content-Type': 'application/json',
          },
          body: JSON.stringify({
              requirements,
              questionCounts,
              difficulties,
              grade,
              textbook,
              班级名称: selectItem.班级名称,
              课程名称: selectItem.课程名称,
              学期名称: selectItem.学期名称
          }),
      });

      if (!response.body) {
        throw new Error('Response body is not readable as a stream');
      }
      const reader = response.body.getReader();
      const decoder = new TextDecoder('utf-8');
      let responseText = "";

      while (true) {
          const { done, value } = await reader.read();
          if (done || stopMsg) {
              setProcessingMessage('');
              setProcessingQuestionResult(false)
              break;
          }
          const chunk = decoder.decode(value, { stream: true });
          const filterText = filterDeepSeekAiResultToText(chunk);
          setProcessingMessage((prevText: string) => prevText + filterText);
          responseText += filterText;
          const markdownContent = responseText.replaceAll('```markdown', '').replaceAll(/```/g, '').replaceAll('\n', '  \n')
          setProcessingMessage(markdownContent);
      }
      if(responseText) {
        const endTime = performance.now()
        const responseTime = Math.round((endTime - startTime) * 100 / 1000) / 100
        console.log("执行时间:", responseTime, responseText, processingMessage)
        const markdownContent = responseText.replaceAll('```markdown', '').replaceAll(/```/g, '').replaceAll('\n', '  \n')
        setProcessingMessage(markdownContent);
        setProcessingQuestionResult(true)
      }

    }
    catch(error: any) {
      console.log("Fetch Error: ", error)
      toast.error("AI生成题库信息失败", { duration: 2000 })
    }
    setGenerating(false)
  };

  const textFieldRef = useRef<HTMLTextAreaElement>(null);
  const containerRef = useRef<HTMLDivElement>(null);

  useEffect(() => {
    console.log("Processing message updated:", processingMessage);
    if (textFieldRef.current) {
      console.log("Scrolling textarea to bottom");
      textFieldRef.current.scrollTop = textFieldRef.current.scrollHeight;
    }
    if (containerRef.current) {
      console.log("Scrolling container to bottom");
      containerRef.current.scrollTop = containerRef.current.scrollHeight;
    }
  }, [processingMessage]);


  return (
    <Grid container spacing={2}>
      {/* 左侧输入区域 */}
      <Grid item style={{ width: 300 }}>
        <ButtonGroup
          variant="contained"
          sx={{
            width: '100%',
            borderRadius: 1
          }}
        >
          {['按照要求生成', '依据资料生成'].map((label, index) => (
            <Button
              key={label}
              onClick={() => setTabIndex(index)}
              variant={tabIndex === index ? 'contained' : 'outlined'}
              sx={{ flex: 1 }}
            >
              {label}
            </Button>
          ))}
        </ButtonGroup>

        {tabIndex === 0 && (
          <Box sx={{ mt: 4 }}>
            {selectItem && selectItem.课程名称 && selectItem.班级名称 && (
              <>
                <Typography sx={{pb: 1, }}>班级: {selectItem.班级名称}</Typography>
                <Typography sx={{pb: 2, pt: 1}}>课程: {selectItem.课程名称}</Typography>
              </>
            )}
            {selectItem== null && (
              <>
                <TextField
                  size="small"
                  select
                  label="课程"
                  fullWidth
                  sx={{ mb: 2 }}
                  value={course}
                  onChange={(e) => setCourse(e.target.value)}
                >
                  {courses.map((course) => (
                    <MenuItem key={course} value={course}>
                      {course}
                    </MenuItem>
                  ))}
                </TextField>
                <TextField
                  size="small"
                  select
                  label="年级"
                  fullWidth
                  sx={{ my: 2 }}
                  value={grade}
                  onChange={(e) => setGrade(e.target.value)}
                >
                  {Object.keys(gradeTextbookMap).map((grade) => (
                    <MenuItem key={grade} value={grade}>
                      {grade}
                    </MenuItem>
                  ))}
                </TextField>
                <TextField
                  size="small"
                  select
                  label="教材"
                  fullWidth
                  sx={{ my: 2 }}
                  value={textbook}
                  onChange={(e) => setTextbook(e.target.value)}
                >
                  {gradeTextbookMap[grade].map((textbook) => (
                    <MenuItem key={textbook} value={textbook}>
                      {textbook}
                    </MenuItem>
                  ))}
                </TextField>
              </>
            )}
            <TextField
              label="题目要求"
              multiline
              rows={4}
              fullWidth
              value={requirements}
              onChange={(e) => setRequirements(e.target.value)}
              sx={{ my: 2 }}
            />

            <Typography variant="subtitle1" gutterBottom>选择题型</Typography>
            <Box sx={{ mb: 2 }}>
              {Object.keys(questionTypeMap).map((type) => (
                <Box key={type} sx={{ mb: 2 }}>
                  <Grid container spacing={1} alignItems="center">
                    <Grid item xs={4}>
                      <Button
                        variant="outlined"
                        size="small"
                        startIcon={questionTypeIcons[type as keyof typeof questionTypeIcons]}
                        fullWidth
                      >
                        {questionTypeMap[type as keyof typeof questionTypeMap]}
                      </Button>
                    </Grid>
                    <Grid item xs={3}>
                      <TextField
                        type="number"
                        value={questionCounts[type]}
                        onChange={(e) => setQuestionCounts((prev: typeof questionCounts) => ({
                          ...prev,
                          [type]: Number(e.target.value)
                        }))}
                        size="small"
                        fullWidth
                        label="数量"
                      />
                    </Grid>
                    <Grid item xs={5}>
                      <TextField
                        select
                        size="small"
                        fullWidth
                        value={difficulties[type as keyof typeof difficulties]}
                        onChange={(e) => setDifficulties((prev: typeof difficulties) => ({
                          ...prev,
                          [type]: e.target.value as 'easy' | 'medium' | 'hard'
                        }))}
                      >
                        {['easy', 'medium', 'hard'].map((diff) => (
                          <MenuItem key={diff} value={diff}>
                            {difficultyMap[diff]}
                          </MenuItem>
                        ))}
                      </TextField>
                    </Grid>
                  </Grid>
                </Box>
              ))}
            </Box>

            <Button
              disabled={generating}
              variant="contained"
              fullWidth
              size="small"
              startIcon={<GenerateIcon />}
              onClick={handleGenerate}
              sx={{ mt: 2 }}
            >
              生成题目
            </Button>

            <Button
              disabled={generating}
              variant="outlined"
              fullWidth
              size="small"
              startIcon={<GenerateIcon />}
              onClick={()=>{
                setPageModel('AppAiQuestionList')
              }}
              sx={{ mt: 4 }}
            >
              返回
            </Button>

          </Box>
        )}
      </Grid>

      {/* 中间Markdown显示区域 */}
      <Grid item style={{ minWidth: 300, width: '35%' }}>
        <Box
          sx={{
            height: "85vh", // 父容器高度为视口高度
            display: "flex",
            flexDirection: "column",
          }}
        >
          <Box
            sx={{
              height: "100%", // 高度为 100%
              border: "1px solid", // 边框（用于可视化）
              borderColor: "gray",
              borderRadius: 1,
            }}
          >
            <TextField
              multiline
              fullWidth
              value={processingMessage?.replace(/\\n/g, "\n") || ""}
              placeholder="生成题目"
              onChange={(e) => setProcessingMessage(e.target.value)}
              sx={{
                height: "100%", // 高度为 100%
                "& .MuiInputBase-root": {
                  height: "100%", // 确保输入框高度为 100%
                  alignItems: "flex-start", // 文本从顶部对齐
                },
                "& textarea": {
                  height: "100% !important", // 确保 <textarea> 高度为 100%
                  overflowY: "auto !important", // 确保 <textarea> 可以滚动
                },
              }}
              inputProps={{ ref: textFieldRef }} // 绑定 ref 到 <textarea>
            />
          </Box>
        </Box>
      </Grid>

      {/* 右侧题目解析区域 */}
      <Grid item style={{ minWidth: 300, width: '35%' }}>
        <Box
          sx={{
            height: "85vh", // 父容器高度为视口高度
            display: "flex",
            flexDirection: "column",
          }}
        >
          <Box sx={{
              height: "100%", // 高度为 100%
              overflowY: "auto", // 启用 Y 轴滚动
              border: "1px solid", // 边框（用于可视化）
              borderColor: 'gray',
              borderRadius: 1,
              pl: 2
            }}>
            {processingQuestionResult == true && (
              <AddOrEditTable authConfig={authConfig} externalId={Number(externalId)} id={addEditActionId} action={'edit_default'} addEditStructInfo={null} open={true} toggleAddTableDrawer={toggleEditTableDrawer} addUserHandleFilter={addUserHandleFilter} backEndApi={backEndApi} editViewCounter={editViewCounter + 1} IsGetStructureFromEditDefault={isGetStructureFromEditDefault} addEditViewShowInWindow={true} CSRF_TOKEN={CSRF_TOKEN} dataGridLanguageCode={store.init_default.dataGridLanguageCode} dialogMaxWidth={store.init_default.dialogMaxWidth} toggleImagesPreviewListDrawer={toggleImagesPreviewListDrawer} handleIsLoadingTipChange={handleIsLoadingTipChange} setForceUpdate={setForceUpdate} additionalParameters={additionalParameters}/>
            )}
            {processingQuestionResult == false && (
              <Typography sx={{py:4, color: 'gray'}}>{'生成题目完成以后,就会显示出题目预览'}</Typography>
            )}
          </Box>
        </Box>
      </Grid>
    </Grid>
  );
};

export default AppAiQuestion;
