import { ApiResponseOptions } from '@nestjs/swagger';

export const MissingOrIncorrectFieldsResponse: ApiResponseOptions = {
	description:
		'Some fields are missing or incorrect. Please check your inputs.',
};
