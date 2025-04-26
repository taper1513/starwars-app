type CustomRadioProps = {
  checked: boolean;
  onChange: () => void;
  label: string;
  name: string;
  value: string;
};

export function CustomRadio({ checked, onChange, label, name, value }: CustomRadioProps) {
  return (
    <label className="flex items-center cursor-pointer select-none">
      <input
        type="radio"
        name={name}
        value={value}
        checked={checked}
        onChange={onChange}
        className="sr-only"
      />
      <span className="relative flex items-center justify-center w-4 h-4 mr-[10px] my-[21px]">
        <span
          className={`block w-4 h-4 rounded-full transition-colors duration-200 border
            ${checked ? 'bg-[#0094ff] border-[#0094ff]' : 'bg-white border-[#d1bfc9]'}
          `}
        />
        {checked && (
          <span className="absolute w-[5px] h-[5px]  rounded-full bg-white" />
        )}
      </span>
      <span className="text-lg">{label}</span>
    </label>
  );
}
