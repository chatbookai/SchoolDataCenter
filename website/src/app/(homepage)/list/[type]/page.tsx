'use client'

import { useParams } from "next/navigation";
import NewsList from '@/views/News/NewsList';

function HelpCenterPage() {
  const params = useParams(); // Use useParams instead of useRouter
  const type = params.type; // Get [type] from URL
  console.log("type", type);

  return <NewsList type={type}/>; // 将 type 作为 props 传递
}

export default HelpCenterPage
