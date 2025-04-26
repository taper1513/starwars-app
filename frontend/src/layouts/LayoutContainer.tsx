import { ReactNode } from 'react';

type LayoutContainerProps = {
  children: ReactNode;
};

export default function LayoutContainer({ children }: LayoutContainerProps) {
  return (
    <div className="flex w-full m-0 flex-3 flex-col items-center gap-7 min-h-screen bg-gray ">
      <div className="bg-white h-[50px] w-full shadow text-center content-center" >
        <h1 className="text-[18px] font-extrabold text-green-600 bg-white">
          <span className="font-black">SWStarter</span>
        </h1>
      </div>
      <div className="flex flex-col items-center gap-12 w-full max-w-7xl mx-auto">
        {children}  
      </div>
    </div>
  );
} 