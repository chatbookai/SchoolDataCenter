import dynamic from "next/dynamic";

const CanvasEditor = dynamic(() => import("./CanvasEditor"), {
  ssr: false, // 关闭服务器端渲染 (SSR)
});

export default function Home() {
  return (
    <CanvasEditor />
  );
}
