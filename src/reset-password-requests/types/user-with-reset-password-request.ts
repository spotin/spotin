import { ResetPasswordRequest } from '@/reset-password-requests/types/reset-password-request';
import { User } from '@/users/types/user';

export type UserWithResetPasswordRequest = User & {
	resetPasswordRequest?: ResetPasswordRequest;
};
