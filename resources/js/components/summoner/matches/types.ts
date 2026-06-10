export interface TraitItem {
    icon?: string | null;
    name?: string | null;
    style?: number;
    num_units?: number;
}

export interface UnitItem {
    character_id: string;
    name?: string | null;
    icon?: string | null;
    rarity: number;
    tier: number;
}

export interface MatchItem {
    placement: number;
    traits: TraitItem[];
    lp: string;
    date: string;
    units: UnitItem[];
}

