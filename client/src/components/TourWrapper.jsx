
import { useTour } from "@reactour/tour";
import { useEffect } from 'react';

export default function TourWrapper(props) {
    const { setIsOpen } = useTour();
    useEffect(() => {
      setIsOpen(true);
    }, []);
    return <div/>
  }