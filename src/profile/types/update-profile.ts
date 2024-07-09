import { Profile } from '@/profile/types/profile';

export type UpdateProfile = Partial<Omit<Profile, 'email'>> & {
	currentPassword: string;
	newPassword?: string;
};
