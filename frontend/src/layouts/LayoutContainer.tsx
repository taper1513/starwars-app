import { ReactNode } from 'react';

type LayoutContainerProps = {
  children: ReactNode;
};

export default function LayoutContainer({ children }: LayoutContainerProps) {
  return (
    <div className="flex w-full flex-col items-center min-h-screen bg-gray p-8">
      <h1 className="text-4xl font-extrabold text-green-600 mb-12">
        <span className="font-black">SW</span>Starter
      </h1>
      <div className="flex flex-col items-center gap-12 w-full max-w-7xl mx-auto">
        {children}  
      </div>
    </div>
  );
} 