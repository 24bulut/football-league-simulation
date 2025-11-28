# Round-Robin Tournament Scheduling Algorithm

## Visual Example (4 Teams: A, B, C, D)

### Initial Setup

```
        A (FIXED - never moves)
       /|\
      / | \
     D  |  B
      \ | /
       \|/
        C
        
Rotating array: [D, B, C]
```

### Round 1

```
Teams:    A     D  B  C
                ↑     ↑
Position: fix   0  1  2

Pairing:
  • A vs D  (fixed vs rotating[0])
  • B vs C  (rotating[1] vs rotating[2])
```

**Week 1 Matches:** `A vs D`, `B vs C`

---

### Round 2 (Rotate: move last to front)

```
Before rotation: [D, B, C]
After rotation:  [C, D, B]

Teams:    A     C  D  B
                ↑     ↑
Position: fix   0  1  2

Pairing:
  • C vs A  (rotating[0] vs fixed) - home/away swapped
  • D vs B  (rotating[1] vs rotating[2])
```

**Week 2 Matches:** `C vs A`, `D vs B`

---

### Round 3 (Rotate again)

```
Before rotation: [C, D, B]
After rotation:  [B, C, D]

Teams:    A     B  C  D
                ↑     ↑
Position: fix   0  1  2

Pairing:
  • A vs B  (fixed vs rotating[0])
  • C vs D  (rotating[1] vs rotating[2])
```

**Week 3 Matches:** `A vs B`, `C vs D`

---

## Complete Schedule (First Half)

| Week | Match 1 | Match 2 |
|------|---------|---------|
| 1    | A vs D  | B vs C  |
| 2    | C vs A  | D vs B  |
| 3    | A vs B  | C vs D  |

## Second Half (Reverse Home/Away)

| Week | Match 1 | Match 2 |
|------|---------|---------|
| 4    | D vs A  | C vs B  |
| 5    | A vs C  | B vs D  |
| 6    | B vs A  | D vs C  |

---
