 form Cross Correlate two Sounds
  sentence input_sound_1
  sentence input_sound_2
  real start_time 0
  real end_time 10
endform

Open long sound file... 'input_sound_1$'
Extract part: 0,10,"no"
Extract one channel... 1
sound1 = selected("Sound")
Open long sound file... 'input_sound_2$'
Extract part: 0,10,"no"
Extract one channel... 1
sound2 = selected("Sound")

select sound1
plus sound2
Cross-correlate: "peak 0.99", "zero"
offset = Get time of maximum: 0, 0, "Sinc70"

writeInfoLine:'offset'
