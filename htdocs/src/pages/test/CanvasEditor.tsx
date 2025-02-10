import React, { useEffect, useRef, useState } from "react";

// 只在客户端加载 @hufe921/canvas-editor 和插件
let Editor: any;
let floatingToolbarPlugin: any;
let barcode1DPlugin: any;
let barcode2DPlugin: any;
let codeblockPlugin: any;
let docxPlugin: any;
let excelPlugin: any;
let diagramPlugin: any;
let casePlugin: any;

if (typeof window !== "undefined") {
  Editor = require("@hufe921/canvas-editor").default;
  floatingToolbarPlugin = require("@hufe921/canvas-editor-plugin-floating-toolbar").default;
  barcode1DPlugin = require("@hufe921/canvas-editor-plugin-barcode1d").default;
  barcode2DPlugin = require("@hufe921/canvas-editor-plugin-barcode2d").default;
  codeblockPlugin = require("@hufe921/canvas-editor-plugin-codeblock").default;
  docxPlugin = require("@hufe921/canvas-editor-plugin-docx").default;
  excelPlugin = require("@hufe921/canvas-editor-plugin-excel").default;
  diagramPlugin = require("@hufe921/canvas-editor-plugin-diagram").default;
  casePlugin = require("@hufe921/canvas-editor-plugin-case").default;
}

const CanvasEditor: React.FC = () => {
  const editorRef = useRef<any>(null);
  const containerRef = useRef<HTMLDivElement | null>(null);
  const [isClient, setIsClient] = useState(false);

  useEffect(() => {
    setIsClient(true);
  }, []);

  const handleInsertBarcode1D = () => {
    if (editorRef.current) {
      editorRef.current.executeInsertBarcode1D("123456789", 100, 50);
    }
  };

  const handleInsertCodeblock = () => {
    if (editorRef.current) {
      editorRef.current.executeInsertCodeblock("console.log('Hello World');");
    }
  };

  const handleImportDocx = async (buffer: ArrayBuffer) => {
    if (editorRef.current) {
      await editorRef.current.executeImportDocx({ arrayBuffer: buffer });
    }
  };


  useEffect(() => {
    if (isClient && containerRef.current && Editor) {

      const editorConfig = {
        header: [{ value: "Header" }],
        main: [{ value: "Hello World" }],
        footer: [{ value: "canvas-editor", size: 12 }],
        page: {
          width: 794,
          height: 1123,
          scale: 1,
          gap: 20,
          mode: "continuity",
          renderMode: "speed",
          margins: [100, 120, 100, 120],
          paperDirection: "vertical",
          background: {
            color: "#FFFFFF",
            image: "",
            size: "cover",
            repeat: "no-repeat",
          },
        },
        text: {
          defaultType: "TEXT",
          defaultColor: "#000000",
          defaultFont: "Microsoft YaHei",
          defaultSize: 16,
          minSize: 5,
          maxSize: 72,
          wordBreak: "break-word",
        },
        formatting: {
          defaultRowMargin: 1,
          defaultBasicRowMarginHeight: 8,
          defaultTabWidth: 32,
          underlineColor: "#000000",
          strikeoutColor: "#FF0000",
          highlightAlpha: 0.6,
          rangeAlpha: 0.6,
          rangeColor: "#AECBFA",
          rangeMinWidth: 5,
          searchMatchAlpha: 0.6,
          searchMatchColor: "#FFFF00",
          searchNavigateMatchColor: "#AAD280",
        },
        watermark: {
          text: "CANVAS-EDITOR",
          color: "#AEB5C0",
          opacity: 0.3,
          size: 120,
          font: "Microsoft YaHei",
          repeat: false,
          gap: [10, 10],
        },
        pageNumber: {
          bottom: 60,
          size: 12,
          font: "Microsoft YaHei",
          color: "#000000",
          format: "第{pageNo}页/共{pageCount}页",
          startPageNo: 1,
        },
        placeholder: {
          text: "请输入正文",
          color: "#DCDFE6",
          opacity: 1,
          size: 16,
          font: "Microsoft YaHei",
        },
        table: {
          tdPadding: [0, 5, 5, 5],
          defaultTrMinHeight: 42,
          defaultColMinWidth: 40,
          defaultBorderColor: "#000000",
        },
        headerOptions: {
          top: 30,
          maxHeightRadio: "half",
          editable: true,
        },
        footerOptions: {
          bottom: 30,
          maxHeightRadio: "half",
          editable: true,
        },
        cursor: {
          width: 1,
          color: "#000000",
          dragWidth: 2,
          dragColor: "#0000FF",
        },
        title: {
          defaultFirstSize: 26,
          defaultSecondSize: 24,
          defaultThirdSize: 22,
          defaultFourthSize: 20,
          defaultFifthSize: 18,
          defaultSixthSize: 16,
        },
        control: {
          placeholderColor: "#9c9b9b",
          bracketColor: "#000000",
          borderWidth: 1,
          borderColor: "#000000",
        },
        checkbox: {
          width: 14,
          height: 14,
          fillStyle: "#5175f4",
          strokeStyle: "#ffffff",
        },
        radio: {
          width: 14,
          height: 14,
          fillStyle: "#5175f4",
          strokeStyle: "#000000",
        },
      };

      const editorInstance = new Editor(containerRef.current, editorConfig);

      // 加载所有插件
      if (floatingToolbarPlugin) editorInstance.use(floatingToolbarPlugin);
      if (barcode1DPlugin) editorInstance.use(barcode1DPlugin);
      if (barcode2DPlugin) editorInstance.use(barcode2DPlugin);
      if (codeblockPlugin) editorInstance.use(codeblockPlugin);
      if (docxPlugin) editorInstance.use(docxPlugin);
      if (excelPlugin) editorInstance.use(excelPlugin);
      if (diagramPlugin) editorInstance.use(diagramPlugin);
      if (casePlugin) editorInstance.use(casePlugin);

      // 存储实例
      editorRef.current = editorInstance;
    }

    return () => {
      editorRef.current?.destroy();
    };
  }, [isClient]);

  return <div ref={containerRef} className="canvas-editor" style={{  }} />;
};

export default CanvasEditor;
