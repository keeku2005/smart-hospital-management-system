<?php
function assignDoctor($symptom) {
    $s = strtolower($symptom);

    // ---------- Level 1: Chest / Heart ----------
    if (preg_match('/chest|heart|palpitation|breathless|bp|blood pressure/', $s)) {
        if (preg_match('/pain|pressure|tight/', $s)) return "Cardiologist";
        return "Cardiologist";
    }

    // ---------- Level 2: Fever / Infection ----------
    if (preg_match('/fever|cold|cough|infection|flu|viral|weakness|body pain/', $s)) {
        if (preg_match('/child|kid|baby/', $s)) return "Pediatrician";
        return "General Physician";
    }

    // ---------- Level 3: Skin ----------
    if (preg_match('/skin|itch|rash|eczema|psoriasis|acne|allergy/', $s)) {
        return "Dermatologist";
    }

    // ---------- Level 4: Bones / Muscles ----------
    if (preg_match('/bone|joint|knee|back|shoulder|fracture|sprain|arthritis/', $s)) {
        return "Orthopedic";
    }

    // ---------- Level 5: ENT ----------
    if (preg_match('/ear|nose|throat|sinus|hearing|tonsil|snoring/', $s)) {
        return "ENT";
    }

    // ---------- Level 6: Eye ----------
    if (preg_match('/eye|vision|blur|red eye|irritation|watery/', $s)) {
        return "Eye Specialist";
    }

    // ---------- Level 7: Stomach / Digestion ----------
    if (preg_match('/stomach|gas|acidity|vomit|diarrhea|constipation|indigestion|ulcer/', $s)) {
        return "Gastroenterologist";
    }

    // ---------- Level 8: Brain / Nerves ----------
    if (preg_match('/headache|migraine|stroke|numb|weakness one side|fit|seizure|memory/', $s)) {
        return "Neurologist";
    }

    // ---------- Level 9: Women Health ----------
    if (preg_match('/period|pregnant|pregnancy|pcos|pcod|vaginal|pelvic|menstrual/', $s)) {
        return "Gynecologist";
    }

    // ---------- Level 10: Dental ----------
    if (preg_match('/tooth|teeth|gum|cavity|bleeding gum|dental|toothache/', $s)) {
        return "Dentist";
    }

    // ---------- Fallback ----------
    return "General Physician";
}
?>
